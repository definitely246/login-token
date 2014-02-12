<?php namespace Definitely246\LoginToken\Interfaces;

interface RouteFilterInterface
{
	public function filter($route, $request);
	public function register($app);
	public function currentToken();
}
