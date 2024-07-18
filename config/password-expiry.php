<?php

use EightyNine\FilamentPasswordExpiry\Http\Middleware\PasswordExpiryMiddleware;
use EightyNine\FilamentPasswordExpiry\Pages\ResetPassword;
use Filament\Facades\Filament;

return [
    
    /**
     * Table
     * 
     * The table to store the password expiry data in.
     */
    'table_name' => 'users',

    /**
     * Column
     * 
     * The column to store the password expiry data in.
     */
    'column_name' => 'password_expires_at',

    /** 
     * Password column name
     * 
     * The name of the password column, will be updated when setting the new password.
     */
    'password_column_name' => 'password',

    /**
     * Expiry
     * 
     * The number of days before the password expires.
     */
    'expires_in' => 30,

    /**
     * Password expiry route
     * 
     * The route to redirect to when the password expires.
     */
    'password_expiry_route' => 'password-expiry.reset-password',

    /**
     * Password expiry path
     * 
     * The path to redirect to when the password expires.
     */
    'password_expiry_path' => 'password-expiry/reset-password',

    /**
     * Password expiry middleware
     * 
     * The middleware to use for password expiry.
     */
    'password_expiry_middleware' => PasswordExpiryMiddleware::class,

    /**
     * Password expiry middleware
     * 
     * The middleware to use for password expiry.
     */
    'password_reset_page' => ResetPassword::class,

    /**
     * Auth class
     * 
     * The auth class to use for password expiry. By default, the package uses Filament::auth()->user(). Make sure the auth class 
     * also contains the column defined in the table_name config.
     */
    'auth_class' => Filament::class,

    /**
     * Email column
     * 
     * The column to store the email in.
     */
    'email_column_name' => 'email',

    /**
     * After password reset redirect to
     * 
     * The route to redirect to after a password reset. By default, the user will be redirected to the login page
     * using "Filament::getLoginUrl()"
     */
    'after_password_reset_redirect' => null,

    /**
     * Override login route
     * 
     * There is a bug in laravel where when you change password, the user is redirected to the login page by default. This override 
     * fixes that bug by defining a login route that redirects to your panel's login page.
     */
    'override_login_route' => true
];
