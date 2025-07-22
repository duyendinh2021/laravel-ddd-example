<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;

enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isUser(): bool
    {
        return $this === self::USER;
    }

    public function isGuest(): bool
    {
        return $this === self::GUEST;
    }

    public function hasPermission(string $permission): bool
    {
        return match($this) {
            self::ADMIN => true,
            self::USER => in_array($permission, ['read', 'write_own']),
            self::GUEST => in_array($permission, ['read']),
        };
    }

    public static function fromString(string $role): self
    {
        return match(strtolower($role)) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            'guest' => self::GUEST,
            default => throw new InvalidArgumentException("Invalid role: {$role}")
        };
    }
}