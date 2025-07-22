<?php

declare(strict_types=1);

namespace App\Application\Commands;

final readonly class UpdateUserCommand
{
    public function __construct(
        public int $userId,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?string $profileImageUrl = null,
        public ?string $timezone = null,
        public ?string $language = null
    ) {}
}