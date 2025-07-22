# Laravel DDD Example - User Management API

A comprehensive Laravel 12 application demonstrating Domain-Driven Design (DDD) principles with a complete User Management API implementation.

## 🏗️ Architecture

This project follows a strict DDD architecture with four main layers:

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

## 🚀 Features

- **Complete User Management**: Registration, authentication, profile management
- **Domain-Driven Design**: Proper separation of concerns with DDD principles
- **Repository Pattern**: Using prettus/l5-repository for data persistence
- **CQRS Implementation**: Separate commands and queries for better organization
- **Event-Driven Architecture**: Domain events for system decoupling
- **RESTful API**: Complete CRUD operations with proper HTTP status codes
- **Authentication**: Laravel Sanctum for API token authentication
- **Validation**: Request validation with custom business rules
- **Testing**: Unit and feature tests for API endpoints

## 📋 Requirements

- PHP 8.3+
- Composer
- SQLite (default) or MySQL/PostgreSQL

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/duyendinh2021/laravel-ddd-example.git
   cd laravel-ddd-example
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite  # For SQLite
   php artisan migrate
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000/api/v1`

## 🐳 Docker Installation (Recommended)

### Development Environment

1. **Clone the repository**
   ```bash
   git clone https://github.com/duyendinh2021/laravel-ddd-example.git
   cd laravel-ddd-example
   ```

2. **Setup environment**
   ```bash
   # Copy Docker development environment
   cp .env.docker.dev .env
   ```

3. **Build and start development containers**
   ```bash
   # Using Make commands (recommended)
   make dev-build
   make dev-up
   
   # Or using docker compose directly
   docker compose build
   docker compose up -d
   ```

4. **Install dependencies and setup database**
   ```bash
   make dev-install    # Install composer dependencies
   make dev-migrate    # Run database migrations
   
   # Or manually:
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate:fresh --seed
   ```

4. **Access the application**
   - API: `http://localhost:8000/api/v1`
   - MailHog (email testing): `http://localhost:8025`

### Production Deployment

1. **Configure environment variables**
   ```bash
   cp .env.docker .env
   # Edit .env with your production values (database passwords, SMTP settings, etc.)
   ```

2. **Build and deploy**
   ```bash
   make prod-build
   make prod-up
   
   # Or using docker compose:
   docker compose -f docker-compose.prod.yml build
   docker compose -f docker-compose.prod.yml up -d
   ```

3. **Initialize production database**
   ```bash
   make prod-shell
   php artisan key:generate
   php artisan migrate
   exit
   ```

### Docker Services

The Docker setup includes:
- **app**: Laravel application with PHP 8.3-FPM
- **webserver**: Nginx web server
- **db**: MySQL 8.0 database
- **redis**: Redis cache and sessions
- **mailhog**: Email testing (development only)

### Available Make Commands

```bash
make help           # Show available commands
make dev-build      # Build development containers
make dev-up         # Start development environment
make dev-down       # Stop development environment
make dev-logs       # Show development logs
make dev-shell      # Access container shell
make dev-test       # Run tests
make prod-build     # Build production containers  
make prod-up        # Start production environment
make clean          # Clean Docker resources
```

## 📚 API Documentation

### Authentication Endpoints

#### Register User
```http
POST /api/v1/register
Content-Type: application/json

{
    "username": "johndoe",
    "email": "john@example.com",
    "password": "SecurePass123!",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "+84123456789",
    "role": "user"
}
```

#### Login User
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "SecurePass123!"
}
```

#### Logout User (Authenticated)
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### User Management Endpoints

#### Get Users (Authenticated)
```http
GET /api/v1/users?role=user&is_active=true&page=1&per_page=10
Authorization: Bearer {token}
```

#### Get User Details (Authenticated)
```http
GET /api/v1/users/{id}
Authorization: Bearer {token}
```

#### Update User (Authenticated)
```http
PUT /api/v1/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Smith",
    "phone": "+84987654321",
    "timezone": "Asia/Ho_Chi_Minh",
    "language": "vi"
}
```

#### Deactivate User (Authenticated)
```http
DELETE /api/v1/users/{id}
Authorization: Bearer {token}
```

#### Get Current User (Authenticated)
```http
GET /api/v1/me
Authorization: Bearer {token}
```

## 🏛️ Project Structure

```
app/
├── Domain/                         # Domain Layer
│   └── User/
│       ├── Entities/              # User entities
│       ├── ValueObjects/          # Email, Password, Phone, Role
│       ├── Events/                # Domain events
│       ├── Services/              # Domain services
│       ├── Contracts/             # Repository interfaces
│       └── Specifications/        # Business rules
│
├── Application/                   # Application Layer
│   ├── Commands/                  # Command objects
│   ├── Handlers/                  # Command handlers
│   ├── Queries/                   # Query objects
│   ├── QueryHandlers/             # Query handlers
│   ├── Services/                  # Application services
│   └── DTOs/                      # Data transfer objects
│
├── Infrastructure/                # Infrastructure Layer
│   ├── Repositories/              # Repository implementations
│   └── Persistence/
│       └── Models/                # Eloquent models
│
└── Http/                         # Presentation Layer
    ├── Controllers/Api/V1/        # API controllers
    ├── Requests/                  # Form requests
    ├── Resources/                 # API resources
    └── Middleware/                # Custom middleware
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Feature/UserApiTest.php

# Run with coverage (if xdebug is installed)
vendor/bin/phpunit --coverage-html coverage/
```

## 🔧 Key Components

### Domain Layer

- **User Entity**: Core business entity with rich domain logic
- **Value Objects**: Email, Password, Phone, Role with validation
- **Domain Events**: UserRegistered, UserUpdated, UserLoggedIn, UserDeactivated
- **Domain Services**: Business logic that doesn't belong to entities
- **Specifications**: Business rules validation

### Application Layer

- **Commands**: RegisterUserCommand, UpdateUserCommand, DeactivateUserCommand
- **Command Handlers**: Process commands and coordinate domain operations
- **Queries**: GetUserQuery, GetUsersQuery for data retrieval
- **Query Handlers**: Process queries and return formatted data
- **Application Services**: High-level application operations

### Infrastructure Layer

- **Repository Implementation**: Using prettus/l5-repository
- **Eloquent Models**: Data persistence layer
- **External Services**: Integration points

### Presentation Layer

- **API Controllers**: RESTful endpoints
- **Request Validation**: Laravel form requests
- **API Resources**: Response formatting
- **Authentication**: Sanctum middleware

## 🎯 DDD Principles Applied

1. **Ubiquitous Language**: Domain terminology used throughout
2. **Bounded Contexts**: Clear separation of User domain
3. **Entities and Value Objects**: Proper domain modeling
4. **Domain Services**: Complex business logic encapsulation
5. **Repository Pattern**: Data access abstraction
6. **Domain Events**: Decoupled communication
7. **Specifications**: Reusable business rules

## 📝 Configuration

### Database Schema

The user table includes:
- Primary key and identification (user_id, username, email)
- Authentication info (password_hash, password_salt)
- Basic info (first_name, last_name, phone)
- Account status (is_active, is_verified, email_verified_at)
- Role system (admin, user, guest)
- Time tracking (created_at, updated_at, last_login_at)
- Additional info (profile_image_url, timezone, language)

### Environment Variables

Key configuration options in `.env`:

```env
APP_NAME="Laravel DDD Example"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

## 🚦 API Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        // Validation errors (if applicable)
    }
}
```

## 🛡️ Security Features

- Password hashing using Argon2ID
- Email validation and uniqueness checks
- Strong password requirements
- API token authentication with Sanctum
- Request validation and sanitization
- Role-based authorization

## 📈 Performance Considerations

- Repository pattern with caching support
- Efficient database queries with proper indexing
- Lazy loading of relationships
- Pagination support for list endpoints

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Laravel Framework
- prettus/l5-repository package
- Domain-Driven Design principles by Eric Evans
- Clean Architecture by Robert C. Martin
