<?php

namespace EightyNine\FilamentPasswordExpiry\Http\Response;

use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class PasswordResetResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $path = config('password-expiry.after_password_reset_redirect')?: Filament::getLoginUrl();
        return redirect()->to($path);
    }
}
