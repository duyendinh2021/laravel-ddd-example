<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\RegisterUserCommand;
use App\Application\Commands\UpdateUserCommand;
use App\Application\Commands\DeactivateUserCommand;
use App\Application\Handlers\RegisterUserHandler;
use App\Application\Handlers\UpdateUserHandler;
use App\Application\Handlers\DeactivateUserHandler;
use App\Application\Queries\GetUserQuery;
use App\Application\Queries\GetUsersQuery;
use App\Application\QueryHandlers\GetUserHandler;
use App\Application\QueryHandlers\GetUsersHandler;
use App\Domain\User\Entities\User;

class UserApplicationService
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
        private UpdateUserHandler $updateUserHandler,
        private DeactivateUserHandler $deactivateUserHandler,
        private GetUserHandler $getUserHandler,
        private GetUsersHandler $getUsersHandler
    ) {}

    public function registerUser(RegisterUserCommand $command): User
    {
        return $this->registerUserHandler->handle($command);
    }

    public function updateUser(UpdateUserCommand $command): User
    {
        return $this->updateUserHandler->handle($command);
    }

    public function deactivateUser(DeactivateUserCommand $command): User
    {
        return $this->deactivateUserHandler->handle($command);
    }

    public function getUser(GetUserQuery $query): ?User
    {
        return $this->getUserHandler->handle($query);
    }

    public function getUsers(GetUsersQuery $query): array
    {
        return $this->getUsersHandler->handle($query);
    }
}