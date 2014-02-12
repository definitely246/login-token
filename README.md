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

You are ready to go. So you could add this route filter to your `filters.php`

```php
/*
|--------------------------------------------------------------------------
| Login token filter
|--------------------------------------------------------------------------
|
| This class handles incoming token requests that are in the route filter
| for login.token. Basically, if a valid token is supplied then we login
| the user in
|
*/
Route::filter('login.token', function($route, $request)
{
	$tokenString = LoginToken::tokenString();

	try
	{
		$token = LoginToken::attempt($tokenString);

		$userId = $token->getAttachment('userId');

		Auth::loginUsingId($userId);

		$token->delete();
	}
	catch (Definitely246\LoginToken\Exceptions\EmptyTokenException $e)
	{
		// don't worry about empty tokens because our auth.basic
		// filter will keep people from accessing the resource
		// but we could handle this or just throw $e; if we wanted
	}
	catch (Definitely246\LoginToken\Exceptions\InvalidTokenException $e)
	{
		// and same reasoning about empty tokens applies to invalid tokens
	}
	catch (Definitely246\LoginToken\Exceptions\ExpiredTokenException $e)
	{
		// go ahead and delete expired tokens
		$token = $e->getToken();
		$token->delete();
	}
});
```

And then in your `routes.php` add something like this

```php
Route::group(['before' => 'login.token|auth.basic'], function()
{
    Route::get('foo', function()
    {
    	return "this route is protected by auth.basic and login.token";
    });
});
```

Next you need a way to generate tokens, for this example we will just add another route to our `routes.php`

```php
Route::get('token', function()
{
    $token = LoginToken::generate(null, ['user_id' => 1]);
    $expired = LoginToken::generate(new DateTime("-1 day"), ['user_id' => 1]);

    return "
    	<p><a href=\"foo\">Go to /foo with no token</a></p>
    	<p><a href=\"foo?login_token={$token->token_string}\">Go to /foo with valid token</a></p>
    	<p><a href=\"foo?login_token=invalidtokenhere\">Go to /foo with invalid token</a></p>
    	<p><a href=\"foo?login_token={$expired->token_string}\">Go to /foo with expired token</a></p>
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

This will return the token string for the page. If tokenString is null then it is extracted from LoginToken::tokenString(). If there is no token found this returns null.

```php
LoginToken::token($tokenString = null);
```

#### tokenString

This will return the current token string for the given request. If the $request is null then we use Laravel's IoC container to resolve the currentRequest for given route.

```php
LoginToken::token($request = null);
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

#### Token driver

This is the driver which persists and fetches the tokens for this package. Default is to use a database driver.

```php
'token_driver' => 'Definitely246\LoginToken\Drivers\LaravelDatabaseTokenDriver',
```

#### Token string

This closure determines how we get the token string from the request object and is used for getting like LoginToke::token() which returns the current token for the current request.

```php
'token_string' => function($request)
{
	return $request->header('X-Auth-Token') ?: $request->input('login_token');
}
```