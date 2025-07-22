<?php

namespace App\Domain\Shared\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class Timestamp
{
    private DateTimeImmutable $value;
    
    public function __construct(DateTimeInterface $value)
    {
        $this->value = $value instanceof DateTimeImmutable 
            ? $value 
            : DateTimeImmutable::createFromInterface($value);
    }
    
    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }
    
    public static function fromString(string $dateString): self
    {
        try {
            return new self(new DateTimeImmutable($dateString));
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Invalid date format: {$dateString}");
        }
    }
    
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        return new self($dateTime);
    }
    
    public function value(): DateTimeImmutable
    {
        return $this->value;
    }
    
    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->value->format($format);
    }
    
    public function toISOString(): string
    {
        return $this->value->format(DateTimeInterface::ISO8601);
    }
    
    public function isAfter(Timestamp $other): bool
    {
        return $this->value > $other->value;
    }
    
    public function isBefore(Timestamp $other): bool
    {
        return $this->value < $other->value;
    }
    
    public function equals(Timestamp $other): bool
    {
        return $this->value == $other->value;
    }
    
    public function addDays(int $days): self
    {
        return new self($this->value->modify("+{$days} days"));
    }
    
    public function subDays(int $days): self
    {
        return new self($this->value->modify("-{$days} days"));
    }
    
    public function __toString(): string
    {
        return $this->format();
    }
}