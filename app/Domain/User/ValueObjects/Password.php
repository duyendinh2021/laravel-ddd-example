<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;

final readonly class Password
{
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 255;

    public function __construct(
        private string $value
    ) {
        if (strlen($value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Password must be at least ' . self::MIN_LENGTH . ' characters');
        }
        
        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Password must not exceed ' . self::MAX_LENGTH . ' characters');
        }
    }

    public static function fromPlainText(string $plainText): self
    {
        return new self($plainText);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function hash(): string
    {
        // Only hash if it's not already hashed
        if (!$this->isHashed()) {
            return password_hash($this->value, PASSWORD_ARGON2ID);
        }
        return $this->value;
    }

    private function isHashed(): bool
    {
        return password_get_info($this->value)['algo'] !== null;
    }

    public function verify(string $plainText): bool
    {
        return password_verify($plainText, $this->value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isStrong(): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $this->value) === 1;
    }
}