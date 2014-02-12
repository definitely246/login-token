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

		$tokenHandler = (new ReflectionClass($config['token_handler']))->newInstanceArgs(array());

		// delegate the handler to an override class
		if (method_exists($tokenHandler, 'setOverride'))
		{
			$tokenHandler->setOverride($config['token_handler_override'], $app);
		}

		$routeFilter = (new ReflectionClass($config['route_filter']))->newInstanceArgs(array($tokenDriver, $tokenHandler));

		$routeFilter->register($this->app);

		// binding that the LoginToken facade uses
		$this->app->bind('login-token', function() use ($tokenDriver)
		{
			return $tokenDriver;
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