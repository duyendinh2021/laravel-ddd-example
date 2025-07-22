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