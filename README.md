login-token
==============

First require the package in your `composer.json`

```js
"require": {
    ...
    "definitely246/login-token": "dev-master"
}
```

After running `composer update`, next add the service provider to `app/config/app.php`

```php
	'Definitely246\LoginToken\LoginTokenServiceProvider',
```

Setup aliases in `app/config/app.php`

```php
	'LoginToken' => 'Definitely246\LoginToken\Facades\LoginToken',
```

Since the tokens are stored in a database table we need to run migrations to generate this table.

```php
	php artisan migrate --package="definitely246/login-token"
```

### Quickstart Example

You are ready to go. Let's look at an example `routes.php` file

```php

	Route::group(['before' => 'login.token'], function()
	{
		Route::get('foo', function($id)
		{
			return LoginToken::current();
		});
	});

	// use this route to generate a new token
	Route::get('token', function()
	{
		$token = LoginToken::generate();

		return $token;
	});

	Event::listen('login.token.empty', function()
	{
		dd('it is empty!');
	});

	Event::listen('login.token.valid', function($token)
	{
		dd('valid token', $token);
	});

	Event::listen('login.token.empty', function($message)
	{
		dd('invalid token', $message);
	});

```

### Verbose Example

You can generate 

```php



```

### Additional Configuration

You can publish the configuration if you need to make adjustments to how this package works. First, publish your config

```php 
	php artisan config:publish "definitely246/login-token"
```

Next change these options how you see fit.

#### Route filter

This is the class which handles the route filtering

```php
	'route_filter' => 'Definitely246\LoginToken\RouteFilters\LaravelRouteFilter',
```

#### Token driver

This is the driver which persists and fetches the tokens for this package. Default is to use a database driver.

```php
	'token_driver' => 'Definitely246\LoginToken\Drivers\LaravelDatabaseTokenDriver',
```
#### On valid token

This is the callback that happens when a token examined by the route filter above is actually valid according to the given token_driver.

```php
	'on_valid_token' => function($token)
	{
		return Event::fire('login.token.valid', $token);
	},
```

#### On invalid token

This is the callback that fires when a token examined by the route filter above is invalid according to the given token_driver.

```php
	'on_invalid_token' => function($message)
	{
		return Event::fire('login.token.invalid', $message);
	},
```

### On empty token

This is the callback that fires when a token examined by the route filter above is empty.

```php
	'on_empty_token' => function()
	{
		return Event::fire('login.token.empty');
	}
```
