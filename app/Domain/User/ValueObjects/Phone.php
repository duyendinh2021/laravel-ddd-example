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
        // Remove all non-digit characters for validation
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Check if it's a valid format (basic validation)
        return preg_match('/^(\+84|0)([3|5|7|8|9])+([0-9]{8})$/', $cleaned) === 1;
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