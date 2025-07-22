<?php

declare(strict_types=1);

namespace App\Application\Commands;

final readonly class DeactivateUserCommand
{
    public function __construct(
        public int $userId,
        public string $reason = 'Manual deactivation'
    ) {}
}