<?php namespace Definitely246\LoginToken\RouteFilters;

use Definitely246\LoginToken\Interfaces\RouteFilterInterface,
	Definitely246\LoginToken\Exceptions\ExpiredTokenException,
	Definitely246\LoginToken\Exceptions\InvalidTokenException;

class LaravelRouteFilter implements RouteFilterInterface
{
	/**
	 * Create a filter for token logins
	 */
	public function __construct($tokenDriver, $onValidToken, $onInvalidToken, $onEmptyToken)
	{
		$this->tokenDriver = $tokenDriver;
		$this->onValidToken = $onValidToken;
		$this->onInvalidToken = $onInvalidToken;
		$this->onEmptyToken = $onEmptyToken;
	}

	/**
	 * Filter the route
	 * 
	 * @param  Route $route
	 * @param  Request $request
	 * @return void
	 */
	public function filter($route, $request)
	{
		$token = $request->header('X-Auth-Token') ?: $request->input('token');

		if (!$token) {
			return call_user_func_array($this->onEmptyToken, array());
		}

		try
		{
			$token = $this->tokenDriver->attempt($token);
		}
		catch (InvalidTokenException $e)
		{
			return call_user_func_array($this->onInvalidToken, array($e->getMessage()));
		}
		catch (ExpiredTokenException $e)
		{
			return call_user_func_array($this->onInvalidToken, array($e->getMessage()));
		}

		return call_user_func_array($this->onValidToken, array($token));
	}

	/**
	 * Register this filter in the application's scope
	 * 
	 * @param  [type] $app [description]
	 * @return [type]      [description]
	 */
	public function register($app)
	{
		$loginFilter = $this;

		$app['router']->filter('login.token', function($route, $request) use ($loginFilter)
		{
			return $loginFilter->filter($route, $request);
		});
	}
	
	/**
	 * Execute the callback on $method
	 * 
	 * @param  $method
	 * @param  $args
	 * @return anything
	 */
	private function callback($method, $args)
    {
        return call_user_func_array($this->$method, $args);
    }
}
