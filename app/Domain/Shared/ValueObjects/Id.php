<?php

namespace App\Domain\Shared\ValueObjects;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

class Id
{
    private string $value;
    
    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Id cannot be empty');
        }
        
        $this->value = $value;
    }
    
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
    
    public static function fromString(string $value): self
    {
        return new self($value);
    }
    
    public function value(): string
    {
        return $this->value;
    }
    
    public function equals(Id $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}