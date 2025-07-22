# Requirements: RESTful API với Laravel 12 + Domain-Driven Design + Prettus Repository

## 1. TỔNG QUAN DỰ ÁN

### 1.1 Mục tiêu
Xây dựng một hệ thống **User Management API** hoàn chỉnh áp dụng Domain-Driven Design với Laravel 12, bao gồm đầy đủ các layer và component của DDD, sử dụng **prettus/l5-repository** để implement Repository pattern chuyên nghiệp.

### 1.2 Phạm vi chức năng
- **User Registration & Authentication**: Đăng ký, đăng nhập, xác thực
- **Profile Management**: Quản lý thông tin cá nhân, avatar, preferences  
- **Role & Permission**: Phân quyền dựa trên role (Admin, User, Guest)
- **Audit Trail**: Theo dõi hoạt động của user
- **Event-Driven Architecture**: Xử lý bất đồng bộ với Events

### 1.3 Kiến trúc DDD
```
┌─────────────────────────────────────────────────┐
│                PRESENTATION LAYER               │
│  Controllers, Requests, Resources, Middleware   │
├─────────────────────────────────────────────────┤
│                APPLICATION LAYER                │
│   Commands, Handlers, Services, DTOs, Queries  │
├─────────────────────────────────────────────────┤
│                 DOMAIN LAYER                    │
│ Entities, Value Objects, Aggregates, Services  │
├─────────────────────────────────────────────────┤
│               INFRASTRUCTURE LAYER              │
│  Repositories, External Services, Persistence   │
└─────────────────────────────────────────────────┘
```

## 2. CẤU TRÚC THƯ MỤC DDD VỚI PRETTUS REPOSITORY

```
app/
├── Http/                           # Presentation Layer
│   ├── Controllers/Api/V1/
│   │   └── UserController.php
│   ├── Requests/
│   │   ├── RegisterUserRequest.php
│   │   ├── UpdateUserRequest.php
│   │   └── LoginUserRequest.php
│   ├── Resources/
│   │   ├── UserResource.php
│   │   └── UserDetailResource.php
│   └── Middleware/
│       └── RoleMiddleware.php
│
├── Application/                    # Application Layer
│   ├── Commands/
│   │   ├── RegisterUserCommand.php
│   │   ├── UpdateUserCommand.php
│   │   └── DeactivateUserCommand.php
│   ├── Handlers/
│   │   ├── RegisterUserHandler.php
│   │   ├── UpdateUserHandler.php
│   │   └── DeactivateUserHandler.php
│   ├── Queries/
│   │   ├── GetUserQuery.php
│   │   ├── GetUsersQuery.php
│   │   └── GetUsersByRoleQuery.php
│   ├── QueryHandlers/
│   │   ├── GetUserHandler.php
│   │   ├── GetUsersHandler.php
│   │   └── GetUsersByRoleHandler.php
│   ├── Services/
│   │   ├── UserApplicationService.php
│   │   └── AuthenticationService.php
│   └── DTOs/
│       ├── UserRegistrationDTO.php
│       ├── UserUpdateDTO.php
│       └── UserQueryDTO.php
│
├── Domain/                         # Domain Layer
│   ├── User/
│   │   ├── Entities/
│   │   │   ├── User.php
│   │   │   └── UserProfile.php
│   │   ├── ValueObjects/
│   │   │   ├── Email.php
│   │   │   ├── Password.php
│   │   │   ├── Phone.php
│   │   │   └── Role.php
│   │   ├── Aggregates/
│   │   │   └── UserAggregate.php
│   │   ├── Events/
│   │   │   ├── UserRegistered.php
│   │   │   ├── UserUpdated.php
│   │   │   ├── UserLoggedIn.php
│   │   │   └── UserDeactivated.php
│   │   ├── Services/
│   │   │   ├── UserDomainService.php
│   │   │   ├── PasswordService.php
│   │   │   └── EmailVerificationService.php
│   │   ├── Contracts/              # Repository Interfaces (Domain)
│   │   │   ├── UserRepositoryInterface.php
│   │   │   └── UserProfileRepositoryInterface.php
│   │   └── Specifications/
│   │       ├── UniqueEmailSpecification.php
│   │       └── StrongPasswordSpecification.php
│   └── Shared/
│       ├── ValueObjects/
│       │   ├── Id.php
│       │   ├── Timestamp.php
│       │   └── Status.php
│       └── Exceptions/
│           ├── DomainException.php
│           └── ValidationException.php
│
└── Infrastructure/                 # Infrastructure Layer
    ├── Models/                     # Eloquent Models
    │   ├── EloquentUser.php
    │   └── EloquentUserProfile.php
    ├── Repositories/               # Repository Implementations
    │   ├── UserRepositoryEloquent.php
    │   └── UserProfileRepositoryEloquent.php
    ├── Criteria/                   # Query Criteria
    │   ├── ActiveUsersCriteria.php
    │   ├── UsersByRoleCriteria.php
    │   └── RecentUsersCriteria.php
    ├── Presenters/                 # Data Presenters
    │   ├── UserPresenter.php
    │   └── UserListPresenter.php
    ├── Transformers/               # Data Transformers
    │   ├── UserTransformer.php
    │   └── UserProfileTransformer.php
    ├── Validators/                 # Repository Validators
    │   ├── UserValidator.php
    │   └── UserUpdateValidator.php
    ├── Services/
    │   ├── EmailService.php
    │   ├── FileUploadService.php
    │   └── CacheService.php
    ├── Persistence/
    │   ├── Migrations/
    │   └── Seeders/
    └── External/
        ├── NotificationService.php
        └── AuditService.php
```

## 3. INSTALLATION VÀ SETUP PRETTUS REPOSITORY

### 3.1 Cài đặt Dependencies
```bash
# Cài đặt thư viện prettus/l5-repository
composer require prettus/l5-repository

# Publish config file
php artisan vendor:publish --provider="Prettus\Repository\Providers\RepositoryServiceProvider"

# Cài đặt Fractal cho Transformer (nếu cần)
composer require league/fractal

# Generate repository classes
php artisan make:repository User
php artisan make:criteria User\\ActiveUsersCriteria
php artisan make:transformer User
php artisan make:presenter User
```

### 3.2 Configuration
```php
// config/repository.php
return [
    'pagination' => [
        'limit' => 20
    ],
    'fractal' => [
        'params' => [
            'include' => 'include'
        ],
        'serializer' => League\Fractal\Serializer\DataArraySerializer::class
    ],
    'cache' => [
        'enabled' => true,
        'minutes' => 30,
        'repository' => 'cache',
        'clean' => [
            'enabled' => true,
            'on' => [
                'create',
                'update',
                'delete'
            ]
        ],
        'params' => [
            'skipCache' => 'skipCache'
        ],
        'allowed' => [
            'only' => null,
            'except' => null
        ]
    ],
    'validation' => [
        'enabled' => true,
        'rules' => [],
        'messages' => [],
        'attributes' => []
    ],
    'generator' => [
        'basePath' => app()->path(),
        'rootNamespace' => app()->getNamespace(),
        'paths' => [
            'repositories' => 'Infrastructure/Repositories',
            'interfaces' => 'Domain/User/Contracts',
            'transformers' => 'Infrastructure/Transformers',
            'presenters' => 'Infrastructure/Presenters',
            'validators' => 'Infrastructure/Validators',
            'controllers' => 'Http/Controllers',
            'provider' => 'Providers',
            'criteria' => 'Infrastructure/Criteria'
        ]
    ]
];
```

## 4. CHI TIẾT CÁC COMPONENT

### 4.1 PRESENTATION LAYER

#### 4.1.1 Controllers
```php
// app/Http/Controllers/Api/V1/UserController.php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\Criteria\ActiveUsersCriteria;
use App\Infrastructure\Criteria\UsersByRoleCriteria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Prettus\Validator\Exceptions\ValidatorException;

class UserController extends Controller 
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        // RequestCriteria sẽ tự động apply filters từ query params
        // URL: /api/v1/users?role=admin&status=active&search=john&per_page=10
        
        if ($request->has('role')) {
            $this->userRepository->pushCriteria(
                new UsersByRoleCriteria($request->role)
            );
        }
        
        if ($request->boolean('only_active')) {
            $this->userRepository->pushCriteria(new ActiveUsersCriteria());
        }
        
        $users = $this->userRepository->paginate(
            $request->get('per_page', 15)
        );
        
        return response()->json($users);
    }

    /**
     * Store a new user
     */
    public function store(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->create($request->validated());
            return response()->json($user, 201);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->getMessageBag()
            ], 422);
        }
    }

    /**
     * Show user detail
     */
    public function show(string $id): JsonResponse  
    {
        $user = $this->userRepository->find($id);
        return response()->json($user);
    }

    /**
     * Update user
     */
    public function update(string $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->update($request->validated(), $id);
            return response()->json($user);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->getMessageBag()
            ], 422);
        }
    }

    /**
     * Delete user
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->userRepository->delete($id);
        
        return response()->json([
            'message' => 'User deleted successfully',
            'deleted' => $deleted
        ]);
    }

    /**
     * Login user
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        // Implementation for authentication
        // This would typically be handled by AuthenticationService
    }
}
```

#### 4.1.2 Requests (Form Request Validation)
```php
// app/Http/Requests/RegisterUserRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Application\DTOs\UserRegistrationDTO;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|regex:/^[0-9]{10,11}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
        ];
    }
    
    public function toDTO(): UserRegistrationDTO
    {
        return new UserRegistrationDTO(
            email: $this->email,
            password: $this->password,
            firstName: $this->first_name,
            lastName: $this->last_name,
            phone: $this->phone,
        );
    }
}
```

### 4.2 DOMAIN LAYER - REPOSITORY CONTRACTS

#### 4.2.1 Repository Interface
```php
// app/Domain/User/Contracts/UserRepositoryInterface.php
<?php

namespace App\Domain\User\Contracts;

use App\Domain\User\Entities\User;
use App\Domain\Shared\ValueObjects\Id;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Role;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Domain-specific methods
     */
    public function findByEmail(Email $email): ?User;
    public function findByRole(Role $role): Collection;
    public function existsWithEmail(Email $email): bool;
    public function findActiveUsers(): Collection;
    public function findRecentUsers(int $days = 30): Collection;
    public function nextIdentity(): Id;
    
    /**
     * Business operations với domain entities
     */
    public function saveUser(User $user): User;
    public function deleteUser(User $user): bool;
    
    /**
     * Query với criteria
     */
    public function getActiveUsersByRole(Role $role): Collection;
    public function getUsersWithProfile(): Collection;
    
    /**
     * Cache management
     */
    public function clearUserCache(string $userId): void;
    public function cacheUser(User $user): void;
}
```

### 4.3 INFRASTRUCTURE LAYER - REPOSITORY IMPLEMENTATION

#### 4.3.1 Eloquent Repository Implementation
```php
// app/Infrastructure/Repositories/UserRepositoryEloquent.php
<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\{Email, Role, Phone, Password, Status};
use App\Domain\Shared\ValueObjects\{Id, Timestamp};
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
            if ($user->id()->value()) {
                // Update existing user
                $eloquentUser = $this->update($data, $user->id()->value());
            } else {
                // Create new user
                $data['id'] = $this->nextIdentity()->value();
                $eloquentUser = $this->create($data);
            }
            
            // Clear cache
            $this->clearUserCache($user->id()->value());
            
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
            Role::from($eloquentUser->role),
            Status::from($eloquentUser->status),
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
            Role::from($userData['role']),
            Status::from($userData['status']),
            Timestamp::fromString($userData['created_at']),
            Timestamp::fromString($userData['updated_at']),
        );
    }
}
```

#### 4.3.2 Eloquent Model
```php
// app/Infrastructure/Models/EloquentUser.php
<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EloquentUser extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'users';
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'email',
        'password_hash',
        'first_name', 
        'last_name',
        'phone',
        'role',
        'status',
        'email_verified_at',
        'last_login_at'
    ];
    
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Relationship với profile
     */
    public function profile()
    {
        return $this->hasOne(EloquentUserProfile::class, 'user_id', 'id');
    }
}
```

### 4.4 CRITERIA CLASSES (QUERY FILTERS)

#### 4.4.1 Active Users Criteria
```php
// app/Infrastructure/Criteria/ActiveUsersCriteria.php
<?php

namespace App\Infrastructure\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class ActiveUsersCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('status', 'active')
                    ->whereNull('deleted_at');
    }
}
```

#### 4.4.2 Users By Role Criteria
```php
// app/Infrastructure/Criteria/UsersByRoleCriteria.php
<?php

namespace App\Infrastructure\Criteria;

use App\Domain\User\ValueObjects\Role;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UsersByRoleCriteria implements CriteriaInterface
{
    private Role $role;
    
    public function __construct($role)
    {
        $this->role = $role instanceof Role ? $role : Role::from($role);
    }
    
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('role', $this->role->value);
    }
}
```

#### 4.4.3 Recent Users Criteria
```php
// app/Infrastructure/Criteria/RecentUsersCriteria.php
<?php

namespace App\Infrastructure\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class RecentUsersCriteria implements CriteriaInterface
{
    private int $days;
    
    public function __construct(int $days = 30)
    {
        $this->days = $days;
    }
    
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $date = now()->subDays($this->days);
        return $model->where('created_at', '>=', $date)
                    ->orderBy('created_at', 'desc');
    }
}
```

### 4.5 PRESENTERS & TRANSFORMERS

#### 4.5.1 User Presenter
```php
// app/Infrastructure/Presenters/UserPresenter.php
<?php

namespace App\Infrastructure\Presenters;

use App\Infrastructure\Transformers\UserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class UserPresenter extends FractalPresenter
{
    /**
     * Transformer class name
     */
    protected array $resourceKeyItem = 'user';
    protected array $resourceKeyCollection = 'users';
    
    public function getTransformer(): string
    {
        return UserTransformer::class;
    }
}
```

#### 4.5.2 User Transformer
```php
// app/Infrastructure/Transformers/UserTransformer.php
<?php

namespace App\Infrastructure\Transformers;

use App\Infrastructure\Models\EloquentUser;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested
     */
    protected array $availableIncludes = ['profile', 'roles'];
    
    /**
     * Resources that are included by default
     */  
    protected array $defaultIncludes = [];
    
    /**
     * Transform the User entity
     */
    public function transform(EloquentUser $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'full_name' => trim($user->first_name . ' ' . $user->last_name),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'email_verified' => !is_null($user->email_verified_at),
            'last_login' => $user->last_login_at?->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
    
    /**
     * Include Profile relationship
     */
    public function includeProfile(EloquentUser $user): ?\League\Fractal\Resource\Item
    {
        $profile = $user->profile;
        
        if (!$profile) {
            return null;
        }
        
        return $this->item($profile, new UserProfileTransformer());
    }
}
```

### 4.6 VALIDATORS

#### 4.6.1 User Validator
```php
// app/Infrastructure/Validators/UserValidator.php
<?php

namespace App\Infrastructure\Validators;

use Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

class UserValidator extends LaravelValidator
{
    /**
     * Validation rules
     */
    protected array $rules = [
        ValidatorInterface::RULE_CREATE => [
            'email' => 'required|email|unique:users,email',
            'password_hash' => 'required|string',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'role' => 'required|in:admin,user,guest',
            'status' => 'required|in:active,inactive,pending',
        ],
        
        ValidatorInterface::RULE_UPDATE => [
            'email' => 'email|unique:users,email,{id}',
            'first_name' => 'string|max:50', 
            'last_name' => 'string|max:50',
            'phone' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'role' => 'in:admin,user,guest',
            'status' => 'in:active,inactive,pending',
        ]
    ];
    
    /**
     * Custom validation messages
     */
    protected array $messages = [
        'email.required' => 'Email là bắt buộc',
        'email.email' => 'Email không đúng định dạng',
        'email.unique' => 'Email này đã được sử dụng trong hệ thống',
        'password_hash.required' => 'Password hash là bắt buộc',
        'first_name.required' => 'Họ là bắt buộc',
        'first_name.max' => 'Họ không được vượt quá 50 ký tự',
        'last_name.required' => 'Tên là bắt buộc',  
        'last_name.max' => 'Tên không được vượt quá 50 ký tự',
        'phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
        'role.required' => 'Role là bắt buộc',
        'role.in' => 'Role phải là: admin, user, hoặc guest',
        'status.required' => 'Trạng thái là bắt buộc',
        'status.in' => 'Trạng thái phải là: active, inactive, hoặc pending',
    ];

    /**
     * Custom attribute names
     */
    protected array $attributes = [
        'email' => 'Địa chỉ email',
        'first_name' => 'Họ',
        'last_name' => 'Tên',
        'phone' => 'Số điện thoại',
        'role' => 'Vai trò',
        'status' => 'Trạng thái',
    ];
}
```

### 4.7 SERVICE PROVIDER BINDINGS

#### 4.7.1 Repository Service Provider
```php
// app/Providers/RepositoryServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\Repositories\UserRepositoryEloquent;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Bind Repository interfaces to implementations
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepositoryEloquent::class
        );
    }
    
    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        //
    }
}
```

#### 4.7.2 App Service Provider Update
```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->register(RepositoryServiceProvider::class);
}
```

### 4.8 APPLICATION LAYER INTEGRATION

#### 4.8.1 Command Handler với Repository
```php
// app/Application/Handlers/RegisterUserHandler.php
<?php

namespace App\Application\Handlers;

use App\Application\Commands\RegisterUserCommand;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\{Email, Password, Phone};
use App\Domain\User\Events\UserRegistered;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Prettus\Validator\Exceptions\ValidatorException;

class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDomainService $userDomainService,
        private EventDispatcher $eventDispatcher,
    ) {}
    
    public function handle(RegisterUserCommand $command): User
    {
        try {
            // 1. Repository validation (infrastructure level)
            $this->userRepository->validator()->with([
                'email' => $command->email,
                'password_hash' => bcrypt($command->password),
                'first_name' => $command->firstName,
                'last_name' => $command->lastName,
                'phone' => $command->phone,
                'role' => 'user',
                'status' => 'active',
            ])->passesOrFail();
            
            // 2. Domain validation (business rules)
            $this->userDomainService->validateRegistration($command);
            
            // 3. Create domain entity
            $user = User::create(
                Email::fromString($command->email),
                Password::fromString($command->password),
                $command->firstName,
                $command->lastName,
                $command->phone ? Phone::fromString($command->phone) : null,
            );
            
            // 4. Save with repository (including cache)
            $savedUser = $this->userRepository->saveUser($user);
            
            // 5. Cache the new user
            $this->userRepository->cacheUser($savedUser);
            
            // 6. Dispatch domain events
            $this->eventDispatcher->dispatch(
                new UserRegistered($savedUser->id(), $savedUser->email())
            );
            
            return $savedUser;
            
        } catch (ValidatorException $e) {
            throw new \App\Domain\Shared\Exceptions\ValidationException(
                'User validation failed: ' . $e->getMessage(),
                $e->getMessageBag()->toArray()
            );
        }
    }
}
```

#### 4.8.2 Query Handler với Criteria
```php
// app/Application/QueryHandlers/GetUsersHandler.php
<?php

namespace App\Application\QueryHandlers;

use App\Application\Queries\GetUsersQuery;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Role;
use App\Infrastructure\Criteria\{ActiveUsersCriteria, UsersByRoleCriteria, RecentUsersCriteria};

class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}
    
    public function handle(GetUsersQuery $query): array
    {
        // Apply criteria based on query parameters
        if ($query->role) {
            $role = Role::from($query->role);
            $this->userRepository->pushCriteria(
                new UsersByRoleCriteria($role)
            );
        }
        
        if ($query->onlyActive) {
            $this->userRepository->pushCriteria(
                new ActiveUsersCriteria()
            );
        }
        
        if ($query->recentDays) {
            $this->userRepository->pushCriteria(
                new RecentUsersCriteria($query->recentDays)
            );
        }
        
        // Return paginated results with presenter applied
        return $this->userRepository->paginate($query->perPage ?? 15);
    }
}
```

## 5. API ENDPOINTS SPECIFICATION

### 5.1 Authentication Endpoints
```http
POST   /api/v1/auth/register     # Đăng ký user mới
POST   /api/v1/auth/login        # Đăng nhập
POST   /api/v1/auth/logout       # Đăng xuất 
POST   /api/v1/auth/refresh      # Refresh token
POST   /api/v1/auth/forgot       # Quên mật khẩu
POST   /api/v1/auth/reset        # Reset mật khẩu
POST   /api/v1/auth/verify       # Xác minh email
```

### 5.2 User Management Endpoints với Query Parameters
```http
GET    /api/v1/users                           # Danh sách users
       ?role=admin                             # Filter theo role
       &status=active                          # Filter theo status
       &only_active=true                       # Chỉ lấy user active
       &recent_days=30                         # User tạo trong 30 ngày
       &search=john                            # Search theo tên/email
       &per_page=10                            # Số lượng mỗi trang
       &include=profile                        # Include profile data

POST   /api/v1/users                          # Tạo user mới (admin only)
GET    /api/v1/users/{id}?include=profile     # Chi tiết user
PUT    /api/v1/users/{id}                     # Cập nhật user
DELETE /api/v1/users/{id}                     # Xóa user (soft delete)
PATCH  /api/v1/users/{id}/status              # Thay đổi trạng thái
```

### 5.3 Profile Management Endpoints
```http
GET    /api/v1/profile           # Thông tin profile của user hiện tại
PUT    /api/v1/profile           # Cập nhật profile
POST   /api/v1/profile/avatar    # Upload avatar
DELETE /api/v1/profile/avatar    # Xóa avatar
GET    /api/v1/profile/activity  # Lịch sử hoạt động
```

## 6. CACHE STRATEGY VỚI PRETTUS REPOSITORY

### 6.1 Automatic Cache
```php
// Repository tự động cache các query
// Cache config trong config/repository.php
'cache' => [
    'enabled' => true,
    'minutes' => 30,
    'repository' => 'cache',
    'clean' => [
        'enabled' => true,
        'on' => ['create', 'update', 'delete']
    ]
]

// Skip cache cho specific query
$users = $this->userRepository->skipCache()->all();

// Manual cache management
$this->userRepository->clearUserCache($userId);
$this->userRepository->cacheUser($user);
```

### 6.2 Cache Tags
```php
// Sử dụng cache tags để group related cache
Cache::tags(['users', 'profiles'])->put($key, $value, $minutes);

// Clear all user-related cache
Cache::tags(['users'])->flush();
```

## 7. TESTING STRATEGY

### 7.1 Repository Testing
```php
// tests/Unit/Infrastructure/Repositories/UserRepositoryEloquentTest.php
<?php

namespace Tests\Unit\Infrastructure\Repositories;

use Tests\TestCase;
use App\Infrastructure\Repositories\UserRepositoryEloquent;
use App\Infrastructure\Models\EloquentUser;
use App\Domain\User\ValueObjects\{Email, Role};
use App\Infrastructure\Criteria\{ActiveUsersCriteria, UsersByRoleCriteria};
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;
    
    private UserRepositoryEloquentTest $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepositoryEloquent::class);
    }
    
    public function test_can_find_user_by_email(): void
    {
        // Given
        $user = EloquentUser::factory()->create([
            'email' => 'test@example.com'
        ]);
        
        // When
        $email = Email::fromString('test@example.com');
        $foundUser = $this->repository->findByEmail($email);
        
        // Then
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->email, $foundUser->email()->value());
    }
    
    public function test_can_apply_active_users_criteria(): void
    {
        // Given
        EloquentUser::factory()->create(['status' => 'active']);
        EloquentUser::factory()->create(['status' => 'inactive']);
        
        // When
        $this->repository->pushCriteria(new ActiveUsersCriteria());
        $users = $this->repository->all();
        
        // Then
        $this->assertCount(1, $users);
    }
    
    public function test_can_filter_users_by_role(): void
    {
        // Given
        EloquentUser::factory()->create(['role' => 'admin']);
        EloquentUser::factory()->create(['role' => 'user']);
        
        // When
        $adminRole = Role::from('admin');
        $admins = $this->repository->findByRole($adminRole);
        
        // Then
        $this->assertCount(1, $admins);
    }
    
    public function test_cache_is_cleared_when_user_deleted(): void
    {
        // Given
        $user = EloquentUser::factory()->create();
        $this->repository->cacheUser($user);
        
        // When
        $this->repository->delete($user->id);
        
        // Then
        $cachedUser = Cache::get("user.{$user->id}");
        $this->assertNull($cachedUser);
    }
}
```

### 7.2 Criteria Testing
```php
// tests/Unit/Infrastructure/Criteria/ActiveUsersCriteriaTest.php
<?php

namespace Tests\Unit\Infrastructure\Criteria;

use Tests\TestCase;
use App\Infrastructure\Criteria\ActiveUsersCriteria;
use App\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActiveUsersCriteriaTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_filters_only_active_users(): void
    {
        // Given
        EloquentUser::factory()->create(['status' => 'active']);
        EloquentUser::factory()->create(['status' => 'inactive']);
        EloquentUser::factory()->create(['status' => 'pending']);
        
        // When
        $criteria = new ActiveUsersCriteria();
        $query = EloquentUser::query();
        $filteredQuery = $criteria->apply($query, null);
        
        // Then
        $this->assertEquals(1, $filteredQuery->count());
        $this->assertEquals('active', $filteredQuery->first()->status);
    }
}
```

### 7.3 Integration Testing
```php
// tests/Feature/Api/V1/UserControllerTest.php
<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_list_users_with_criteria(): void
    {
        // Given
        EloquentUser::factory()->create(['role' => 'admin', 'status' => 'active']);
        EloquentUser::factory()->create(['role' => 'user', 'status' => 'active']);
        EloquentUser::factory()->create(['role' => 'admin', 'status' => 'inactive']);
        
        // When
        $response = $this->getJson('/api/v1/users?role=admin&only_active=true');
        
        // Then
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.role', 'admin');
        $response->assertJsonPath('data.0.status', 'active');
    }
    
    public function test_can_create_user_with_validation(): void
    {
        // When
        $response = $this->postJson('/api/v1/users', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0123456789'
        ]);
        
        // Then
        $response->assertCreated();
        $response->assertJsonStructure([
            'id',
            'email', 
            'full_name',
            'role',
            'status',
            'created_at'
        ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
    }
}
```

## 8. PERFORMANCE OPTIMIZATION

### 8.1 Database Indexing
```sql
-- Migration for optimal indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status_role ON users(status, role);  
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_user_profiles_user_id ON user_profiles(user_id);
CREATE INDEX idx_users_deleted_at ON users(deleted_at);

-- Composite indexes for common queries
CREATE INDEX idx_users_role_status_created ON users(role, status, created_at);
CREATE INDEX idx_users_status_email ON users(status, email);
```

### 8.2 Query Optimization với Eloquent
```php
// Eager loading để tránh N+1 problem
$users = $this->model->with(['profile', 'roles'])->get();

// Select specific columns
$users = $this->model->select(['id', 'email', 'first_name', 'last_name'])->get();

// Chunk large datasets
$this->model->chunk(1000, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

### 8.3 Cache Optimization
```php
// Repository cache với tags
public function findActiveUsersByRole(Role $role): Collection
{
    $cacheKey = "users.active.role.{$role->value}";
    
    return Cache::tags(['users', 'active_users'])
        ->remember($cacheKey, 1800, function () use ($role) {
            return $this->getActiveUsersByRole($role);
        });
}

// Cache invalidation
public function saveUser(User $user): User
{
    $savedUser = parent::saveUser($user);
    
    // Clear related cache
    Cache::tags(['users'])->flush();
    
    return $savedUser;
}
```

## 9. DEPLOYMENT CONFIGURATION

### 9.1 Environment Setup
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_management
DB_USERNAME=root
DB_PASSWORD=

# Redis Cache
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Repository Cache
REPOSITORY_CACHE_ENABLED=true
REPOSITORY_CACHE_MINUTES=30

# Queue
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### 9.2 Artisan Commands
```bash
# Installation
composer install --optimize-autoloader --no-dev
composer require prettus/l5-repository
php artisan vendor:publish --provider="Prettus\Repository\Providers\RepositoryServiceProvider"

# Generate Repository structure
php artisan make:repository User
php artisan make:criteria User\\ActiveUsersCriteria
php artisan make:transformer User
php artisan make:presenter User

# Database setup
php artisan migrate
php artisan db:seed --class=UserSeeder

# Queue worker
php artisan queue:work redis --sleep=3 --tries=3 --timeout=90

# Cache optimization
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 10. KẾT LUẬN

### 10.1 Lợi ích của Prettus Repository với DDD

#### **Tính năng mạnh mẽ:**
1. **Auto Query Filtering**: RequestCriteria tự động filter từ URL parameters
2. **Presenter Pattern**: Format response nhất quán mà không cần viết nhiều code
3. **Criteria System**: Combine nhiều filter logic một cách linh hoạt
4. **Auto Validation**: Validate data ngay tại repository level
5. **Cache Integration**: Cache tự động với strategy thông minh
6. **Pagination**: Built-in pagination với metadata

#### **Developer Experience:**
1. **Less Boilerplate**: Ít code lặp lại, focus vào business logic
2. **Consistency**: Chuẩn hóa cách implement repository across team
3. **Flexibility**: Có thể override methods khi cần custom logic
4. **Testing**: Dễ test với interface contracts

#### **Performance Benefits:**
1. **Smart Caching**: Auto cache với invalidation strategies
2. **Query Optimization**: Built-in eager loading và query optimization
3. **Memory Efficiency**: Chunk processing cho large datasets

### 10.2 Trade-offs cần lưu ý

#### **Complexity:**
- Thêm learning curve cho team
- Nhiều abstraction layers
- Config phức tạp hơn cho advanced features

#### **Dependencies:**
- Lock-in với prettus/l5-repository
- Phụ thuộc vào League/Fractal cho transformers
- Cần maintain khi thư viện update

#### **Performance:**
- Overhead của presenter/transformer layer
- Memory usage cao hơn với cache
- Có thể over-engineering cho simple CRUD

### 10.3 Khuyến nghị Implementation

#### **Phase 1: Core Setup**
1. Setup basic repository với User entity
2. Implement 2-3 basic criteria (Active, ByRole)
3. Setup caching với Redis
4. Basic API endpoints với validation

#### **Phase 2: Advanced Features**  
1. Implement Presenters/Transformers
2. Advanced criteria combinations
3. Event-driven architecture
4. Comprehensive testing

#### **Phase 3: Optimization**
1. Performance tuning
2. Advanced caching strategies
3. Monitoring và logging
4. Documentation

#### **Best Practices:**
- **Start Simple**: Implement core features trước, optimize sau
- **Test First**: Viết test cho repository contracts
- **Cache Strategy**: Plan cache invalidation từ đầu
- **Team Training**: Đảm bảo team hiểu về patterns được sử dụng
- **Documentation**: Document business rules và repository usage
- **Performance Monitoring**: Track query performance và cache hit rates

### 10.4 Kết quả mong đợi

Với implementation này, bạn sẽ có:

- ✅ **Professional Repository Layer** với full-featured capabilities  
- ✅ **Clean Architecture** tuân theo DDD principles
- ✅ **High Performance** với intelligent caching
- ✅ **Developer Friendly** với less boilerplate code
- ✅ **Scalable Foundation** cho future features
- ✅ **Maintainable Codebase** với clear separation of concerns

**Repository pattern với prettus/l5-repository là lựa chọn optimal cho Laravel DDD project cần balance giữa functionality, performance và developer experience.**
