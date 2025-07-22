<?php

namespace App\Infrastructure\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class ActiveUsersCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('status', 'active')
                    ->whereNull('deleted_at');
    }
}