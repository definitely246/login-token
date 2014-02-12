<?php namespace Definitely246\LoginToken\Handlers;

use ReflectionClass,
	Definitely246\LoginToken\Interfaces\TokenHandlerInterface;

class LaravelTokenHandler implements TokenHandlerInterface
{
	/**
	 * delegate variable that lets us override the methods in this class
	 * 
	 * @var [type]
	 */
	private $override = null;

	/**
	 * Returns a new override class
	 * @return null
	 */
	public function getOverride()
	{
		if ($this->override && class_exists($this->override))
		{
			return (new ReflectionClass($this->override))->newInstance();
		}

		return null;
	}

	/**
	 * Since this method is set, we are going to delegate things to the
	 * $override class.
	 * 
	 * @param string
	 */
	public function setOverride($override)
	{
		$this->override = $override;
	}

	/**
	 * Calls when the token is valid
	 * 
	 * @param  LoginToken $token
	 * @return <any>
	 */
	public function onValidToken($token)
	{
		$override = $this->getOverride();

		if ($override)
		{
			return $override->onValidToken($token);
		}

		// do nothing, since it is valid
	}

	/**
	 * Calls when the token is invalid
	 * 
	 * @param  Exception $exception
	 * @return <any>
	 */
	public function onInvalidToken($exception)
	{
		$override = $this->getOverride();

		if ($override)
		{
			return $override->onInvalidToken($exception);
		}

		throw $exception;
	}

	/**
	 * Calls when the token is empty
	 * 
	 * @return <any>
	 */
	public function onEmptyToken()
	{
		$override = $this->getOverride();

		if ($override)
		{
			return $override->onEmptyToken();
		}
	}
}