<?php

declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Domain\User\Entities\User;
use InvalidArgumentException;

class UserDomainService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function canRegisterWithEmail(Email $email): bool
    {
        return !$this->userRepository->existsByEmail($email);
    }

    public function canRegisterWithUsername(string $username): bool
    {
        return !$this->userRepository->existsByUsername($username);
    }

    public function validateRegistration(Email $email, string $username): void
    {
        if (!$this->canRegisterWithEmail($email)) {
            throw new InvalidArgumentException('Email is already in use');
        }

        if (!$this->canRegisterWithUsername($username)) {
            throw new InvalidArgumentException('Username is already taken');
        }
    }

    public function authenticateUser(Email $email, string $plainPassword): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->canLogin()) {
            return null;
        }

        if (!$user->getPassword()->verify($plainPassword)) {
            return null;
        }

        return $user;
    }
}