<?php

namespace EightyNine\FilamentPasswordExpiry;

use Filament\Facades\Filament;

class PasswordExpiry
{
    public static function get(): PasswordExpiry
    {
        return app(PasswordExpiry::class);
    }

    public static function make(): PasswordExpiry
    {
        return app(PasswordExpiry::class);
    }

    public static function getPasswordExpiryRoute(): string
    {
        $route = Filament::getCurrentPanel()->generateRouteName(
            config('password-expiry.password_expiry_route')
        );
        return $route;
    }
}
