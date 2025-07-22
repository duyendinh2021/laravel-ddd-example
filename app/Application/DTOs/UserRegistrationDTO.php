<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final readonly class UserRegistrationDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $firstName,
        public string $lastName,
        public ?string $phone = null,
        public string $role = 'user',
        public string $timezone = 'Asia/Ho_Chi_Minh',
        public string $language = 'vi'
    ) {}
}