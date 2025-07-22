<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\{Email, Role, Phone, Password};
use App\Domain\Shared\ValueObjects\{Id, Status, Timestamp};
use App\Infrastructure\Models\EloquentUser;
use App\Infrastructure\Presenters\UserPresenter;
use App\Infrastructure\Validators\UserValidator;
use App\Infrastructure\Criteria\{ActiveUsersCriteria, UsersByRoleCriteria, RecentUsersCriteria};
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserRepositoryEloquent extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EloquentUser::class;
    }
    
    /**
     * Specify Presenter class name  
     */
    public function presenter(): string
    {
        return UserPresenter::class;
    }
    
    /**
     * Specify Validator class name
     */
    public function validator(): string
    {
        return UserValidator::class;
    }
    
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * Domain-specific implementations
     */
    public function findByEmail(Email $email): ?User
    {
        $cacheKey = "user.email.{$email->value()}";
        
        return Cache::remember($cacheKey, 1800, function () use ($email) {
            $eloquentUser = $this->model->where('email', $email->value())->first();
            return $eloquentUser ? $this->toDomainEntity($eloquentUser) : null;
        });
    }
    
    public function findByRole(Role $role): Collection
    {
        $this->pushCriteria(new UsersByRoleCriteria($role));
        $users = $this->all();
        
        return $users->map(function($user) {
            return is_array($user) ? $this->arrayToDomainEntity($user) : $user;
        });
    }
    
    public function existsWithEmail(Email $email): bool
    {
        return $this->model->where('email', $email->value())->exists();
    }
    
    public function findActiveUsers(): Collection
    {
        $this->pushCriteria(new ActiveUsersCriteria());
        $users = $this->all();
        
        return $users->map(function($user) {
            return is_array($user) ? $this->arrayToDomainEntity($user) : $user;
        });
    }
    
    public function findRecentUsers(int $days = 30): Collection
    {
        $this->pushCriteria(new RecentUsersCriteria($days));
        $users = $this->all();
        
        return $users->map(function($user) {
            return is_array($user) ? $this->arrayToDomainEntity($user) : $user;
        });
    }
    
    public function getActiveUsersByRole(Role $role): Collection
    {
        $this->pushCriteria(new ActiveUsersCriteria());
        $this->pushCriteria(new UsersByRoleCriteria($role));
        
        $users = $this->all();
        
        return $users->map(function($user) {
            return is_array($user) ? $this->arrayToDomainEntity($user) : $user;
        });
    }
    
    public function getUsersWithProfile(): Collection
    {
        $users = $this->model->with('profile')->get();
        return $users->map(function($user) {
            return $this->toDomainEntity($user);
        });
    }
    
    public function saveUser(User $user): User
    {
        $data = [
            'email' => $user->email()->value(),
            'password_hash' => $user->password()->hash(),
            'first_name' => $user->firstName(),
            'last_name' => $user->lastName(),
            'phone' => $user->phone()?->value(),
            'role' => $user->role()->value,
            'status' => $user->status()->value,
        ];
        
        try {
            if ($user->id() && $user->id()->value()) {
                // Update existing user
                $eloquentUser = $this->update($data, $user->id()->value());
            } else {
                // Create new user
                $data['id'] = $this->nextIdentity()->value();
                $eloquentUser = $this->create($data);
            }
            
            // Clear cache
            if ($user->id() && $user->id()->value()) {
                $this->clearUserCache($user->id()->value());
            }
            
            return $this->toDomainEntity($eloquentUser);
            
        } catch (\Exception $e) {
            throw new \App\Domain\Shared\Exceptions\DomainException(
                "Failed to save user: " . $e->getMessage()
            );
        }
    }
    
    public function deleteUser(User $user): bool
    {
        $deleted = $this->delete($user->id()->value());
        
        if ($deleted) {
            $this->clearUserCache($user->id()->value());
        }
        
        return $deleted;
    }
    
    public function nextIdentity(): Id
    {
        return Id::generate();
    }
    
    public function clearUserCache(string $userId): void
    {
        Cache::forget("user.{$userId}");
        Cache::forget("user.profile.{$userId}");
        // Clear related cache keys
        Cache::tags(['users'])->flush();
    }
    
    public function cacheUser(User $user): void
    {
        $cacheKey = "user.{$user->id()->value()}";
        Cache::put($cacheKey, $user, 1800); // 30 minutes
    }
    
    /**
     * Convert Eloquent model to Domain Entity
     */
    private function toDomainEntity($eloquentUser): User
    {
        if (is_array($eloquentUser)) {
            return $this->arrayToDomainEntity($eloquentUser);
        }
        
        return User::fromState(
            Id::fromString($eloquentUser->id),
            Email::fromString($eloquentUser->email),
            Password::fromHash($eloquentUser->password_hash),
            $eloquentUser->first_name,
            $eloquentUser->last_name,
            $eloquentUser->phone ? Phone::fromString($eloquentUser->phone) : null,
            Role::fromString($eloquentUser->role),
            Status::fromString($eloquentUser->status),
            Timestamp::fromString($eloquentUser->created_at),
            Timestamp::fromString($eloquentUser->updated_at),
        );
    }
    
    /**
     * Convert array (from presenter) to Domain Entity
     */
    private function arrayToDomainEntity(array $userData): User
    {
        return User::fromState(
            Id::fromString($userData['id']),
            Email::fromString($userData['email']),
            Password::fromHash($userData['password_hash'] ?? ''),
            $userData['first_name'],
            $userData['last_name'],
            isset($userData['phone']) ? Phone::fromString($userData['phone']) : null,
            Role::fromString($userData['role']),
            Status::fromString($userData['status']),
            Timestamp::fromString($userData['created_at']),
            Timestamp::fromString($userData['updated_at']),
        );
    }
    
    // Original interface methods for backward compatibility
    public function findById(int $id): ?User
    {
        $eloquentUser = $this->find($id);
        return $eloquentUser ? $this->toDomainEntity($eloquentUser) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $eloquentUser = $this->findByField('username', $username)->first();
        return $eloquentUser ? $this->toDomainEntity($eloquentUser) : null;
    }

    public function save(User $user): User
    {
        return $this->saveUser($user);
    }

    public function deleteUserById(int $id): bool
    {
        $deleted = $this->delete($id);
        
        if ($deleted) {
            $this->clearUserCache((string)$id);
        }
        
        return $deleted > 0;
    }

    public function findAll(): array
    {
        return $this->all()->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByRoleString(string $role): array
    {
        $roleVO = Role::fromString($role);
        return $this->findByRole($roleVO)->toArray();
    }

    public function findActive(): array
    {
        return $this->findActiveUsers()->toArray();
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->existsWithEmail($email);
    }

    public function existsByUsername(string $username): bool
    {
        return $this->findByField('username', $username)->count() > 0;
    }
}