<?php

namespace EightyNine\FilamentPasswordExpiry;

use EightyNine\FilamentPasswordExpiry\Http\Middleware\PasswordExpiryMiddleware;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

class PasswordExpiryPlugin implements Plugin
{
    public function getId(): string
    {
        return 'password-expiry';
    }

    public function register(Panel $panel): void
    {
        $panel->middleware([
            PasswordExpiryMiddleware::class
        ]);
    }

    public function boot(Panel $panel): void
    {        
        Route::redirect('/login', Filament::getLoginUrl())->name('login');
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
