<?php

namespace EightyNine\FilamentPasswordExpiry\Commands;

use Illuminate\Console\Command;

class FilamentPasswordExpiryCommand extends Command
{
    public $signature = 'filament-password-expiry';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
