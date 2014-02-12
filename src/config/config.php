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
	| token_handler
	|--------------------------------------------------------------------------
	|
	| This is the token handler which will handle valid, invalid and empty token
	| requests.
	|
	*/
	'token_handler' => 'Definitely246\LoginToken\Handlers\LaravelTokenHandler',

	/*
	|--------------------------------------------------------------------------
	| token_handler_override
	|--------------------------------------------------------------------------
	|
	| If this class is present in the default namespace it will be used as the
	| token handler for all requests. This allows users to easily create their own
	| token handlers.
	|
	*/
	'token_handler_override' => 'OverrideLoginTokenHandler'
);