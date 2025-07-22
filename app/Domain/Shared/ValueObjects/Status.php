<?php

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

class Status
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const PENDING = 'pending';
    public const SUSPENDED = 'suspended';
    
    private const VALID_STATUSES = [
        self::ACTIVE,
        self::INACTIVE,
        self::PENDING,
        self::SUSPENDED,
    ];
    
    public readonly string $value;
    
    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid status: {$value}. Must be one of: " . implode(', ', self::VALID_STATUSES)
            );
        }
        
        $this->value = $value;
    }
    
    public static function fromString(string $value): self
    {
        return new self($value);
    }
    
    public static function active(): self
    {
        return new self(self::ACTIVE);
    }
    
    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }
    
    public static function pending(): self
    {
        return new self(self::PENDING);
    }
    
    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }
    
    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }
    
    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }
    
    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }
    
    public function isSuspended(): bool
    {
        return $this->value === self::SUSPENDED;
    }
    
    public function equals(Status $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}