<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final readonly class UserQueryDTO
{
    public function __construct(
        public ?string $role = null,
        public ?bool $isActive = null,
        public ?bool $isVerified = null,
        public ?string $search = null,
        public int $page = 1,
        public int $perPage = 10
    ) {}
}