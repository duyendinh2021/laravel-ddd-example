<?php

declare(strict_types=1);

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Role;
use App\Domain\User\Events\UserRegistered;
use App\Domain\User\Events\UserUpdated;
use App\Domain\User\Events\UserLoggedIn;
use App\Domain\User\Events\UserDeactivated;
use DateTimeImmutable;
use DateTimeInterface;

class User
{
    private array $events = [];

    public function __construct(
        private ?int $userId,
        private string $username,
        private Email $email,
        private Password $password,
        private string $firstName,
        private string $lastName,
        private ?Phone $phone,
        private bool $isActive,
        private bool $isVerified,
        private ?DateTimeInterface $emailVerifiedAt,
        private Role $role,
        private DateTimeInterface $createdAt,
        private DateTimeInterface $updatedAt,
        private ?DateTimeInterface $lastLoginAt = null,
        private ?string $profileImageUrl = null,
        private string $timezone = 'Asia/Ho_Chi_Minh',
        private string $language = 'vi',
        private ?string $passwordSalt = null
    ) {}

    public static function register(
        string $username,
        Email $email,
        Password $password,
        string $firstName,
        string $lastName,
        ?Phone $phone = null,
        Role $role = Role::USER
    ): self {
        $now = new DateTimeImmutable();
        
        $user = new self(
            userId: null,
            username: $username,
            email: $email,
            password: $password,
            firstName: $firstName,
            lastName: $lastName,
            phone: $phone,
            isActive: true,
            isVerified: false,
            emailVerifiedAt: null,
            role: $role,
            createdAt: $now,
            updatedAt: $now
        );

        return $user;
    }

    public function recordRegistration(): void
    {
        $this->addEvent(UserRegistered::create(
            $this->userId ?? 0,
            $this->email->value(),
            $this->username
        ));
    }

    public function updateProfile(
        ?string $firstName = null,
        ?string $lastName = null,
        ?Phone $phone = null,
        ?string $profileImageUrl = null,
        ?string $timezone = null,
        ?string $language = null
    ): void {
        $updatedFields = [];
        
        if ($firstName !== null && $firstName !== $this->firstName) {
            $this->firstName = $firstName;
            $updatedFields['first_name'] = $firstName;
        }
        
        if ($lastName !== null && $lastName !== $this->lastName) {
            $this->lastName = $lastName;
            $updatedFields['last_name'] = $lastName;
        }
        
        if ($phone !== null && !$phone->equals($this->phone)) {
            $this->phone = $phone;
            $updatedFields['phone'] = $phone->value();
        }
        
        if ($profileImageUrl !== null && $profileImageUrl !== $this->profileImageUrl) {
            $this->profileImageUrl = $profileImageUrl;
            $updatedFields['profile_image_url'] = $profileImageUrl;
        }
        
        if ($timezone !== null && $timezone !== $this->timezone) {
            $this->timezone = $timezone;
            $updatedFields['timezone'] = $timezone;
        }
        
        if ($language !== null && $language !== $this->language) {
            $this->language = $language;
            $updatedFields['language'] = $language;
        }

        if (!empty($updatedFields)) {
            $this->updatedAt = new DateTimeImmutable();
            $this->addEvent(UserUpdated::create($this->userId ?? 0, $updatedFields));
        }
    }

    public function changePassword(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->updatedAt = new DateTimeImmutable();
        $this->addEvent(UserUpdated::create($this->userId ?? 0, ['password']));
    }

    public function verifyEmail(): void
    {
        if (!$this->isVerified) {
            $this->isVerified = true;
            $this->emailVerifiedAt = new DateTimeImmutable();
            $this->updatedAt = new DateTimeImmutable();
            $this->addEvent(UserUpdated::create($this->userId ?? 0, ['email_verified']));
        }
    }

    public function recordLogin(): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
        $this->addEvent(UserLoggedIn::create($this->userId ?? 0, $this->email->value()));
    }

    public function deactivate(string $reason = 'Manual deactivation'): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->updatedAt = new DateTimeImmutable();
            $this->addEvent(UserDeactivated::create($this->userId ?? 0, $reason));
        }
    }

    public function activate(): void
    {
        if (!$this->isActive) {
            $this->isActive = true;
            $this->updatedAt = new DateTimeImmutable();
            $this->addEvent(UserUpdated::create($this->userId ?? 0, ['activated']));
        }
    }

    public function changeRole(Role $newRole): void
    {
        if ($this->role !== $newRole) {
            $this->role = $newRole;
            $this->updatedAt = new DateTimeImmutable();
            $this->addEvent(UserUpdated::create($this->userId ?? 0, ['role' => $newRole->value]));
        }
    }

    // Getters
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function getEmailVerifiedAt(): ?DateTimeInterface
    {
        return $this->emailVerifiedAt;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->profileImageUrl;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getPasswordSalt(): ?string
    {
        return $this->passwordSalt;
    }

    // Event handling
    private function addEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function clearEvents(): void
    {
        $this->events = [];
    }

    // Business logic methods
    public function canLogin(): bool
    {
        return $this->isActive && $this->isVerified;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role->hasPermission($permission);
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }
}