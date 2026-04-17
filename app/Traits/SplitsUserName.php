<?php

namespace App\Traits;

use App\Models\User;

trait SplitsUserName
{
    /**
     * Automatically split name into first and last name before saving
     */
    protected static function bootSplitsUserName()
    {
        static::creating(function ($model) {
            if ($model->name && ! $model->first_name) {
                $nameParts = User::splitName($model->name);
                $model->first_name = $nameParts['first_name'];
                $model->last_name = $nameParts['last_name'];
            }
        });

        static::updating(function ($model) {
            // If name is being changed, resplit the names
            if ($model->isDirty('name')) {
                $nameParts = User::splitName($model->name);
                $model->first_name = $nameParts['first_name'];
                $model->last_name = $nameParts['last_name'];
            }
        });
    }
}
