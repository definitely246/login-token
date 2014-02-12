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

		$tokenDriverClassName = $this->app->config->get('login-token::config.token_driver');

		$tokenDriver = (new ReflectionClass($tokenDriverClassName))->newInstanceArgs(array($this->app));

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