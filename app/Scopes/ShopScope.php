<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ShopScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // if (auth('sanctum')->check()) {
        //     $user = auth('sanctum')->user();
        //     if (!$user->hasRole('super-admin')) {
        //         $builder->where('shop_id', $user->shop_id);
        //     }
        // }

        if (auth('api')->check()) {
            $user = auth('api')->user();
            if (!$user->hasRole('super-admin')) {
                $builder->where('shop_id', $user->shop_id);
            }
        }
    }
}