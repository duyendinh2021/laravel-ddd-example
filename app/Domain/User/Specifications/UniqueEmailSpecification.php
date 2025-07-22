<?php

declare(strict_types=1);

namespace App\Domain\User\Specifications;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;

class UniqueEmailSpecification
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function isSatisfiedBy(Email $email): bool
    {
        return !$this->userRepository->existsByEmail($email);
    }
}