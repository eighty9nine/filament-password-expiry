<?php

namespace EightyNine\FilamentPasswordExpiry;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use EightyNine\FilamentPasswordExpiry\Commands\FilamentPasswordExpiryCommand;
use EightyNine\FilamentPasswordExpiry\Pages\ResetPassword;
use EightyNine\FilamentPasswordExpiry\Testing\TestsFilamentPasswordExpiry;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class PasswordExpiryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'password-expiry';

    public static string $viewNamespace = 'password-expiry';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('eightynine/password-expiry');
            })
            ->hasRoutes($this->getRoutes());

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-password-expiry/{$file->getFilename()}"),
                ], 'filament-password-expiry-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentPasswordExpiry());

        Livewire::component('eighty-nine.filament-password-expiry.pages.reset-password', ResetPassword::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'eightynine/filament-password-expiry';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-password-expiry', __DIR__ . '/../resources/dist/components/filament-password-expiry.js'),
            Css::make('filament-password-expiry-styles', __DIR__ . '/../resources/dist/filament-password-expiry.css'),
            Js::make('filament-password-expiry-scripts', __DIR__ . '/../resources/dist/filament-password-expiry.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentPasswordExpiryCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [
            '/../routes/web',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'add_password_expiry_column_to_table',
        ];
    }
}
