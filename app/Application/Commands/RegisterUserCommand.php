<?php

declare(strict_types=1);

namespace App\Application\Commands;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $firstName,
        public string $lastName,
        public ?string $phone = null,
        public string $role = 'user'
    ) {}
}