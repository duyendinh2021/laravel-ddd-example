<?php

namespace App\Infrastructure\Presenters;

use App\Infrastructure\Transformers\UserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class UserPresenter extends FractalPresenter
{
    /**
     * Transformer class name
     */
    protected array $resourceKeyItem = 'user';
    protected array $resourceKeyCollection = 'users';
    
    public function getTransformer(): string
    {
        return UserTransformer::class;
    }
}