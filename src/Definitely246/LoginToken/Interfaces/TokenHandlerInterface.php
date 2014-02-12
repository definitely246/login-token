<?php namespace Definitely246\LoginToken\Interfaces;

interface TokenHandlerInterface
{
	public function onValidToken($token);
	public function onInvalidToken($exception);
	public function onExpiredToken($token, $exception);
	public function onEmptyToken();
}
