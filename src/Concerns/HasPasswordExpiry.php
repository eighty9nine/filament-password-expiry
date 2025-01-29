<?php

namespace EightyNine\FilamentPasswordExpiry\Concerns;

use EightyNine\FilamentPasswordExpiry\Exceptions\ColumnsNotFoundException;
use Illuminate\Support\Facades\Schema;

trait HasPasswordExpiry
{
    public static function bootHasPasswordExpiry()
    {
        if (!Schema::hasColumn(config('password-expiry.table_name'), config('password-expiry.column_name'))) {
            throw new ColumnsNotFoundException(__('password-expiry::password-expiry.reset-password.exceptions.column_not_found', [
                'column_name' => config('password-expiry.column_name'),
                'password_column_name' => config('password-expiry.password_column_name'),
                'table_name' => config('password-expiry.table_name'),
            ]));
        }

        static::creating(function ($model) {
            if (
                filled($model->{config('password-expiry.password_column_name')}) &&
                !config('password-expiry.change_password_first_login')
            ) {
                $model->{config('password-expiry.column_name')} = now()->addDays(config('password-expiry.expires_in'));
            }
        });

        static::updating(function ($model) {
            if (
                $model->isDirty(config('password-expiry.password_column_name')) &&
                filled($model->{config('password-expiry.password_column_name')})
            ) {
                $model->{config('password-expiry.column_name')} = now()->addDays(config('password-expiry.expires_in'));
            }
        });
    }
}
