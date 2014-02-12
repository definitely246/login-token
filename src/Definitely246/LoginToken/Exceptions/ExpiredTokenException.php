<?php namespace Definitely246\LoginToken\Exceptions;

use Exception;

class ExpiredTokenException extends Exception
{
	public function __construct($message, $token)
	{
		parent::__construct($message);
		$this->token = $token;
	}

	public function getToken()
	{
		return $this->token;
	}
}