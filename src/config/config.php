<?php return array(

	/*
	|--------------------------------------------------------------------------
	| route_filter
	|--------------------------------------------------------------------------
	|
	| This is the route filter that will be used to filter routes
	| 
	|
	*/
	'route_filter' => 'Definitely246\LoginToken\RouteFilters\LaravelRouteFilter',

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
	| on_valid_token
	|--------------------------------------------------------------------------
	|
	| This is the callback that happens when a token examined by the route
	| filter above is actually valid according to the given token_driver.
	|
	*/
	'on_valid_token' => function($token)
	{
		return Event::fire('login.token.valid', $token);
	},

	/*
	|--------------------------------------------------------------------------
	| on_invalid_token
	|--------------------------------------------------------------------------
	|
	| This is the callback that fires when a token examined by the route filter
	| above is invalid according to the given token_driver.
	|
	*/
	'on_invalid_token' => function($message)
	{
		return Event::fire('login.token.invalid', $message);
	},

	/*
	|--------------------------------------------------------------------------
	| on_empty_token
	|--------------------------------------------------------------------------
	|
	| This is the callback that fires when a token examined by the route filter
	| above is empty.
	|
	*/
	'on_empty_token' => function()
	{
		return Event::fire('login.token.empty');
	}
);