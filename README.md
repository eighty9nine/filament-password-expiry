# Allow your users to periodically reset their passwords, to enforce security.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eightynine/filament-password-expiry.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-password-expiry)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-password-expiry/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eightynine/filament-password-expiry/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-password-expiry/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eightynine/filament-password-expiry/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eightynine/filament-password-expiry.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-password-expiry)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require eightynine/filament-password-expiry
```

### Add the plugin to your panel

1. In you panel's provider, add the plugin as:
```php

use EightyNine\FilamentPasswordExpiry\PasswordExpiryPlugin;
            
    public function panel(Panel $panel): Panel
    {
        return $panel
            ...
            ->plugin(PasswordExpiryPlugin::make());

    }
```
2. Publish the migrations and config file in order to set up the password expiry table and column.
```bash
php artisan vendor:publish --tag="password-expiry-migrations"
php artisan vendor:publish --tag="password-expiry-config"
php artisan migrate
```
3. In your authentication class, example `app/Models/User.php`, add the has password expiry trait to the model, the trait checks if all is setup correctly and will throw an exception if not. The trait will update the password_expires_at column when the user is created.
```php
use EightyNine\FilamentPasswordExpiry\Concerns\HasPasswordExpiry;
            
class User extends Authenticatable
{
    use HasPasswordExpiry;
    ...
}

You are all good to go! Now when a user is created, the password_expires_at column will be updated with the current date and time plus the expires_in config value. When the user tries to login, the middleware will check if the password_expires_at column is less than the current date and time. If it is, the user will be redirected to the password expiry page.

This is the contents of the published config file:

```php
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
```

## Credits

- [Eighty Nine](https://github.com/eighty9nine)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
