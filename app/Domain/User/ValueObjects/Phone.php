<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;

final readonly class Phone
{
    public function __construct(
        private string $value
    ) {
        if (!$this->isValidPhoneNumber($value)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
    }

    private function isValidPhoneNumber(string $phone): bool
    {
        // Remove all non-digit characters except + for validation
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Check if it's a valid format (more flexible validation)
        // Supports Vietnamese phone numbers: +84 followed by mobile prefixes and 8 digits
        // Or local format: 0 followed by mobile prefixes and 8 digits  
        return preg_match('/^(\+84[3-9]\d{8}|0[3-9]\d{8})$/', $cleaned) === 1;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Phone $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}