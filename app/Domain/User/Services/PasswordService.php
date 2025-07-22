<?php

declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\ValueObjects\Password;

class PasswordService
{
    public function generateSecurePassword(int $length = 12): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@$!%*?&';
        $password = '';
        
        // Ensure at least one of each required character type
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[random_int(0, 25)]; // Uppercase
        $password .= 'abcdefghijklmnopqrstuvwxyz'[random_int(0, 25)]; // Lowercase
        $password .= '0123456789'[random_int(0, 9)]; // Number
        $password .= '@$!%*?&'[random_int(0, 6)]; // Special char
        
        // Fill the rest randomly
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return str_shuffle($password);
    }

    public function hashPassword(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_ARGON2ID);
    }

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function isPasswordStrong(string $password): bool
    {
        return Password::fromPlainText($password)->isStrong();
    }
}