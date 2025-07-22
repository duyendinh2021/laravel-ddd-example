<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Role;
use App\Infrastructure\Persistence\Models\UserModel;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use DateTime;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return UserModel::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findById(int $id): ?User
    {
        $userModel = $this->find($id);
        return $userModel ? $this->mapToDomainEntity($userModel) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $userModel = $this->findByField('email', $email->value())->first();
        return $userModel ? $this->mapToDomainEntity($userModel) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $userModel = $this->findByField('username', $username)->first();
        return $userModel ? $this->mapToDomainEntity($userModel) : null;
    }

    public function save(User $user): User
    {
        $data = $this->mapToArray($user);
        
        if ($user->getUserId()) {
            $userModel = $this->update($data, $user->getUserId());
        } else {
            $userModel = $this->create($data);
        }

        return $this->mapToDomainEntity($userModel);
    }

    public function deleteUser(int $id): bool
    {
        return $this->delete($id) > 0;
    }

    public function findAll(): array
    {
        return $this->all()->map(fn($model) => $this->mapToDomainEntity($model))->toArray();
    }

    public function findByRole(string $role): array
    {
        return $this->findByField('role', $role)
            ->map(fn($model) => $this->mapToDomainEntity($model))
            ->toArray();
    }

    public function findActive(): array
    {
        return $this->findByField('is_active', true)
            ->map(fn($model) => $this->mapToDomainEntity($model))
            ->toArray();
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->findByField('email', $email->value())->count() > 0;
    }

    public function existsByUsername(string $username): bool
    {
        return $this->findByField('username', $username)->count() > 0;
    }

    private function mapToDomainEntity(UserModel $userModel): User
    {
        return new User(
            userId: $userModel->user_id,
            username: $userModel->username,
            email: new Email($userModel->email),
            password: Password::fromHash($userModel->password_hash),
            firstName: $userModel->first_name,
            lastName: $userModel->last_name,
            phone: $userModel->phone ? new Phone($userModel->phone) : null,
            isActive: $userModel->is_active,
            isVerified: $userModel->is_verified,
            emailVerifiedAt: $userModel->email_verified_at,
            role: Role::fromString($userModel->role),
            createdAt: $userModel->created_at,
            updatedAt: $userModel->updated_at,
            lastLoginAt: $userModel->last_login_at,
            profileImageUrl: $userModel->profile_image_url,
            timezone: $userModel->timezone ?? 'Asia/Ho_Chi_Minh',
            language: $userModel->language ?? 'vi',
            passwordSalt: $userModel->password_salt
        );
    }

    private function mapToArray(User $user): array
    {
        return [
            'username' => $user->getUsername(),
            'email' => $user->getEmail()->value(),
            'password_hash' => $user->getPassword()->hash(),
            'password_salt' => $user->getPasswordSalt(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhone()?->value(),
            'is_active' => $user->isActive(),
            'is_verified' => $user->isVerified(),
            'email_verified_at' => $user->getEmailVerifiedAt(),
            'role' => $user->getRole()->value,
            'last_login_at' => $user->getLastLoginAt(),
            'profile_image_url' => $user->getProfileImageUrl(),
            'timezone' => $user->getTimezone(),
            'language' => $user->getLanguage(),
            'updated_at' => $user->getUpdatedAt(),
        ];
    }
}