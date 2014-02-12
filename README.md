login-token
==============



Since the tokens are stored in a database table we need to run migrations to generate this table.

```php
	php artisan migrate --package="definitely246/login-token"
```


Use tokens to login as a user or fire events in Laravel 4.