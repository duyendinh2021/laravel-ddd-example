<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\Repositories\UserRepository;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\Services\PasswordService;
use App\Application\Handlers\RegisterUserHandler;
use App\Application\Handlers\UpdateUserHandler;
use App\Application\Handlers\DeactivateUserHandler;
use App\Application\QueryHandlers\GetUserHandler;
use App\Application\QueryHandlers\GetUsersHandler;
use App\Application\Services\UserApplicationService;
use App\Application\Services\AuthenticationService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Domain services
        $this->app->singleton(UserDomainService::class);
        $this->app->singleton(PasswordService::class);

        // Application handlers
        $this->app->singleton(RegisterUserHandler::class);
        $this->app->singleton(UpdateUserHandler::class);
        $this->app->singleton(DeactivateUserHandler::class);
        $this->app->singleton(GetUserHandler::class);
        $this->app->singleton(GetUsersHandler::class);

        // Application services
        $this->app->singleton(UserApplicationService::class);
        $this->app->singleton(AuthenticationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}