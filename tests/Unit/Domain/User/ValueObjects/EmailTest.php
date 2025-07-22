<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\Email;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class EmailTest extends TestCase
{
    /** @test */
    public function it_accepts_valid_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->value());
    }

    /** @test */
    public function it_rejects_invalid_email()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('invalid-email');
    }

    /** @test */
    public function it_can_compare_emails()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }
}