<?php return array(

	/*
	|--------------------------------------------------------------------------
	| token_driver
	|--------------------------------------------------------------------------
	|
	| This is the token driver that is used to check and see if a token is
	| valid or invalid.
	|
	*/
	'token_driver' => 'Definitely246\LoginToken\Drivers\LaravelDatabaseTokenDriver',

	/*
	|--------------------------------------------------------------------------
	| token_string
	|--------------------------------------------------------------------------
	|
	| This closure determines how we get the token string from the request object
	| and is used for getting like LoginToke::token() which returns the current
	| token for the current request.
	|
	*/
	'token_string' => function($request)
	{
		return $request->header('X-Auth-Token') ?: $request->input('login_token');
	}
);