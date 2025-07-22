<?php

declare(strict_types=1);

namespace App\Domain\User\Events;

use DateTimeImmutable;

final readonly class UserDeactivated
{
    public function __construct(
        public int $userId,
        public string $reason,
        public DateTimeImmutable $occurredOn
    ) {}

    public static function create(int $userId, string $reason): self
    {
        return new self(
            userId: $userId,
            reason: $reason,
            occurredOn: new DateTimeImmutable()
        );
    }
}