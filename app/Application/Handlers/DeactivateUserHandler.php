<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\DeactivateUserCommand;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use InvalidArgumentException;

class DeactivateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(DeactivateUserCommand $command): User
    {
        $user = $this->userRepository->findById($command->userId);
        
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        $user->deactivate($command->reason);

        return $this->userRepository->save($user);
    }
}