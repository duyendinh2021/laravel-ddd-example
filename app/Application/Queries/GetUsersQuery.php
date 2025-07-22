<?php

declare(strict_types=1);

namespace App\Application\Queries;

final readonly class GetUsersQuery
{
    public function __construct(
        public ?string $role = null,
        public ?bool $isActive = null,
        public ?bool $isVerified = null,
        public int $page = 1,
        public int $perPage = 10
    ) {}
}