<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class PasswordTest extends TestCase
{
    /** @test */
    public function it_accepts_valid_password()
    {
        $password = Password::fromPlainText('SecurePass123!');
        $this->assertInstanceOf(Password::class, $password);
    }

    /** @test */
    public function it_rejects_short_password()
    {
        $this->expectException(InvalidArgumentException::class);
        Password::fromPlainText('short');
    }

    /** @test */
    public function it_can_hash_password()
    {
        $password = Password::fromPlainText('SecurePass123!');
        $hash = $password->hash();
        $this->assertNotEquals('SecurePass123!', $hash);
        $this->assertTrue(password_verify('SecurePass123!', $hash));
    }

    /** @test */
    public function it_can_verify_password()
    {
        $password = Password::fromPlainText('SecurePass123!');
        $this->assertTrue($password->verify('SecurePass123!'));
        $this->assertFalse($password->verify('wrongpassword'));
    }

    /** @test */
    public function it_detects_strong_password()
    {
        $strongPassword = Password::fromPlainText('SecurePass123!');
        $this->assertTrue($strongPassword->isStrong());
        
        $weakPassword = Password::fromPlainText('password123');
        $this->assertFalse($weakPassword->isStrong());
    }
}