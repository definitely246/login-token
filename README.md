login-token
==============

### Installation

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

// you don't have to define this class but then you probably want to edit your config or
// you are stuck with the default out of the box token handler when the route filters run

class LaravelTokenHandler implements Definitely246\LoginToken\Interfaces\TokenHandlerInterface
{
	public function onValidToken($token)
	{
		// dd('valid token', $token);
		if (!Auth::check())
		{
			$userId = $token->getAttachment('user_id');
			Auth::loginUsingId($userId);
		}
	}

	public function onInvalidToken($exception)
	{
		// dd('invalid token', $exception);
		throw $exception;
	}

	public function onEmptyToken()
	{
		// dd('empty token');
		return Redirect::to('login');
	}
}

Route::group(['before' => 'login.token|auth'], function()
{
    Route::get('foo', function()
    {
    	dd('here is current token', LoginToken::token());
    });
});

// use this route to generate a new token
Route::get('token', function()
{
    $token = LoginToken::generate(null, ['user_id' => 1]);

    return "
    	<p><a href=\"foo\">Go to /foo with no token</a></p>
    	<p><a href=\"foo?login_token={$token->token_string}\">Go to /foo with valid token</a></p>
    	<p><a href=\"foo?login_token=invalidtokenhere\">Go to /foo with invalid token</a></p>
    	";
});

```

### About LoginToken Facade

Below are methods from the LoginToken facade

#### attempt

This will return a valid token that matches the tokenString.

If the token string is not found then InvalidTokenException is thrown.
If the token string is expired then an ExpiredTokenException is thrown.

```php
	LoginToken::attempt($tokenString);
```

#### check

This will find a token that matches the token string and return it to you.
If the token string is not found then you will receive null.

```php
	LoginToken::check($tokenString);
```

#### generate

This will persist a new LoginToken with the given expires_at and attachments.
$expires_at is a DateTime and $attachments should be an array of additional things you want to attach to this token.
This will return the newly generated LoginToken.

```php
	LoginToken::generate($expires_at = null, $attachments = array());
```

#### token

This will return the current logged in token for the page. If there is not one, it is simply null.

```php
	LoginToken::token();
```

#### logout

This will remove whatever token you happen to pass to logout method. In the example below we are removing the current token.

```php
	$token = LoginToken::token();
	LoginToken::logout($token);
```

#### refresh

This will update and persist changes to an existing token. In the example below we are extending the expiration time for 1 hour on the current token.

```php
	$token = LoginToken::token();
	$token->expires_at = new DateTime('+1 hour');
	LoginToken::refresh($token)
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

#### Token handler

This is the default class which handles what should happen when a route filter kicks in.

```php
	'token_handler' => 'Definitely246\LoginToken\Handlers\LaravelTokenHandler',
```

#### Token handler override

This is the override class which (if defined) handles what should happen when a route filter kicks in.

```php
	'token_handler_override' => 'LaravelTokenHandler',
```


