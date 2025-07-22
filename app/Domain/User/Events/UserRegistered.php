<?php

declare(strict_types=1);

namespace App\Domain\User\Events;

use DateTimeImmutable;

final readonly class UserRegistered
{
    public function __construct(
        public int $userId,
        public string $email,
        public string $username,
        public DateTimeImmutable $occurredOn
    ) {}

    public static function create(int $userId, string $email, string $username): self
    {
        return new self(
            userId: $userId,
            email: $email,
            username: $username,
            occurredOn: new DateTimeImmutable()
        );
    }
}