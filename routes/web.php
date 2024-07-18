<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Filament\Panel;


Route::name('filament.')
    ->group(function () {
        foreach (Filament::getPanels() as $panel) {
            /** @var Panel $panel */
            $panelId = $panel->getId();
            $domains = $panel->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($panel->getMiddleware())
                    ->name("{$panelId}." . ((filled($domain) && (count($domains) > 1)) ? "{$domain}." : ''))
                    ->prefix($panel->getPath())
                    ->group(function () use ($panel) {
                        Route::get(config('password-expiry.password_expiry_path'), config('password-expiry.password_reset_page'))
                            ->name(config('password-expiry.password_expiry_route'))
                            ->middleware($panel->getAuthMiddleware());
                    });
            }
        }
    });
if(config("password-expiry.override_login_route")){
    Route::name('login')
        ->get('/login', function(){
            return redirect()->to(config('password-expiry.after_password_reset_redirect')?: Filament::getLoginUrl());
        });
}