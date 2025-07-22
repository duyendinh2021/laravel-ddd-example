<?php

declare(strict_types=1);

namespace App\Domain\User\Events;

use DateTimeImmutable;

final readonly class UserLoggedIn
{
    public function __construct(
        public int $userId,
        public string $email,
        public DateTimeImmutable $occurredOn
    ) {}

    public static function create(int $userId, string $email): self
    {
        return new self(
            userId: $userId,
            email: $email,
            occurredOn: new DateTimeImmutable()
        );
    }
}