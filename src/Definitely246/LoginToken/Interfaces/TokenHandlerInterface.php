<?php namespace Definitely246\LoginToken\Interfaces;

interface TokenHandlerInterface
{
	public function onValidToken($token);
	public function onInvalidToken($message);
	public function onEmptyToken();
}
