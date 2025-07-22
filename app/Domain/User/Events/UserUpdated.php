<?php

declare(strict_types=1);

namespace App\Domain\User\Events;

use DateTimeImmutable;

final readonly class UserUpdated
{
    public function __construct(
        public int $userId,
        public array $updatedFields,
        public DateTimeImmutable $occurredOn
    ) {}

    public static function create(int $userId, array $updatedFields): self
    {
        return new self(
            userId: $userId,
            updatedFields: $updatedFields,
            occurredOn: new DateTimeImmutable()
        );
    }
}