<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\Role;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class RoleTest extends TestCase
{
    /** @test */
    public function it_can_create_roles_from_string()
    {
        $adminRole = Role::fromString('admin');
        $this->assertEquals(Role::ADMIN, $adminRole);
        
        $userRole = Role::fromString('user');
        $this->assertEquals(Role::USER, $userRole);
        
        $guestRole = Role::fromString('guest');
        $this->assertEquals(Role::GUEST, $guestRole);
    }

    /** @test */
    public function it_rejects_invalid_role()
    {
        $this->expectException(InvalidArgumentException::class);
        Role::fromString('invalid');
    }

    /** @test */
    public function it_has_permission_checks()
    {
        $admin = Role::ADMIN;
        $user = Role::USER;
        $guest = Role::GUEST;

        $this->assertTrue($admin->hasPermission('read'));
        $this->assertTrue($admin->hasPermission('write_own'));
        $this->assertTrue($admin->hasPermission('delete'));

        $this->assertTrue($user->hasPermission('read'));
        $this->assertTrue($user->hasPermission('write_own'));
        $this->assertFalse($user->hasPermission('delete'));

        $this->assertTrue($guest->hasPermission('read'));
        $this->assertFalse($guest->hasPermission('write_own'));
        $this->assertFalse($guest->hasPermission('delete'));
    }

    /** @test */
    public function it_has_role_type_checks()
    {
        $this->assertTrue(Role::ADMIN->isAdmin());
        $this->assertFalse(Role::ADMIN->isUser());
        $this->assertFalse(Role::ADMIN->isGuest());

        $this->assertFalse(Role::USER->isAdmin());
        $this->assertTrue(Role::USER->isUser());
        $this->assertFalse(Role::USER->isGuest());

        $this->assertFalse(Role::GUEST->isAdmin());
        $this->assertFalse(Role::GUEST->isUser());
        $this->assertTrue(Role::GUEST->isGuest());
    }
}