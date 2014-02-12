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

You are ready to go. Let's look at an example...

```php
	// in routes.php
	Route::group(['before' => 'login.token'], function()
	{
		Route::get('foo', function()
		{
			return array('foo' => 'bar');
		});
	});
```