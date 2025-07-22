<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\Entities\User;

class AuthenticationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDomainService $userDomainService
    ) {}

    public function authenticate(string $email, string $password): ?User
    {
        $emailVO = new Email($email);
        $user = $this->userDomainService->authenticateUser($emailVO, $password);

        if ($user) {
            $user->recordLogin();
            $this->userRepository->save($user);
        }

        return $user;
    }

    public function getUserByEmail(string $email): ?User
    {
        $emailVO = new Email($email);
        return $this->userRepository->findByEmail($emailVO);
    }
}