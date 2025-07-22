<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\RegisterUserCommand;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Role;
use App\Domain\User\Services\UserDomainService;
use InvalidArgumentException;

class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDomainService $userDomainService
    ) {}

    public function handle(RegisterUserCommand $command): User
    {
        $email = new Email($command->email);
        
        // Domain validation
        $this->userDomainService->validateRegistration($email, $command->username);

        // Create domain entities
        $password = Password::fromPlainText($command->password);
        $phone = $command->phone ? new Phone($command->phone) : null;
        $role = Role::fromString($command->role);

        // Create user entity
        $user = User::register(
            username: $command->username,
            email: $email,
            password: $password,
            firstName: $command->firstName,
            lastName: $command->lastName,
            phone: $phone,
            role: $role
        );

        // Save user
        $savedUser = $this->userRepository->save($user);
        
        // Record registration event
        $savedUser->recordRegistration();

        return $savedUser;
    }
}