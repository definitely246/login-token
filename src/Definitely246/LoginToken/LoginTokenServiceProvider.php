<?php namespace Definitely246\LoginToken;

use ReflectionClass, Illuminate\Support\ServiceProvider;

class LoginTokenServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('definitely246/login-token');

		$app = $this->app;

		$config = $this->app->config->get('login-token::config');

		$tokenDriver = (new ReflectionClass($config['token_driver']))->newInstanceArgs(array($this->app));

		$onValidToken = $config['on_valid_token']; $onInvalidToken = $config['on_invalid_token']; $onEmptyToken = $config['on_empty_token'];

		$routeFilter = (new ReflectionClass($config['route_filter']))->newInstanceArgs(array($tokenDriver, $onValidToken, $onInvalidToken, $onEmptyToken));

		$routeFilter->register($this->app);

		$this->app->bind('login-token', function() use ($tokenDriver)
		{
			return $tokenDriver->current();
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// do nothing when this package registers
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}