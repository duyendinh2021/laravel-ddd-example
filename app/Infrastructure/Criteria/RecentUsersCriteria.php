<?php

namespace App\Infrastructure\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class RecentUsersCriteria implements CriteriaInterface
{
    private int $days;
    
    public function __construct(int $days = 30)
    {
        $this->days = $days;
    }
    
    /**
     * Apply criteria in current Query
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $date = now()->subDays($this->days);
        return $model->where('created_at', '>=', $date)
                    ->orderBy('created_at', 'desc');
    }
}