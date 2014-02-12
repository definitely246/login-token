<?php namespace Definitely246\LoginToken\RouteFilters;

use Definitely246\LoginToken\Interfaces\RouteFilterInterface,
	Definitely246\LoginToken\Exceptions\ExpiredTokenException,
	Definitely246\LoginToken\Exceptions\InvalidTokenException;

class LaravelRouteFilter implements RouteFilterInterface
{
	/**
	 * Create a filter for token logins
	 */
	public function __construct($tokenDriver, $tokenHandler)
	{
		$this->tokenDriver = $tokenDriver;
		$this->tokenHandler = $tokenHandler;
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
		$token = $request->header('X-Auth-Token') ?: $request->input('login_token');

		if (!$token) {
			return $this->tokenHandler->onEmptyToken();
		}

		try
		{
			$token = $this->tokenDriver->attempt($token);
		}
		catch (InvalidTokenException $e)
		{
			return $this->tokenHandler->onInvalidToken($e);
		}
		catch (ExpiredTokenException $e)
		{
			return $this->tokenHandler->onInvalidToken($e);
		}

		return $this->tokenHandler->onValidToken($token);
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
}
