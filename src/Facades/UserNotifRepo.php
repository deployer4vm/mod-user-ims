<?php

namespace hpsynapse\moduser\Facades;

use Illuminate\Support\Facades\Facade;

class UserNotifRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \hpsynapse\moduser\Contracts\UserNotifRepo::class;
    }
}