<?php

namespace App\Infrastructure\Criteria;

use App\Domain\User\ValueObjects\Role;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UsersByRoleCriteria implements CriteriaInterface
{
    private Role $role;
    
    public function __construct($role)
    {
        $this->role = $role instanceof Role ? $role : Role::fromString($role);
    }
    
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('role', $this->role->value);
    }
}