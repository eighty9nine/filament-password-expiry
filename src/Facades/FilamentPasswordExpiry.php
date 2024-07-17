<?php

namespace EightyNine\FilamentPasswordExpiry\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EightyNine\FilamentPasswordExpiry\FilamentPasswordExpiry
 */
class FilamentPasswordExpiry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \EightyNine\FilamentPasswordExpiry\FilamentPasswordExpiry::class;
    }
}
