<?php namespace Definitely246\LoginToken\Facades;

use Illuminate\Support\Facades\Facade;

class LoginToken extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'login-token'; }
}