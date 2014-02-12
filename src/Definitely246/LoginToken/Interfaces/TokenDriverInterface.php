<?php namespace Definitely246\LoginToken\Interfaces;

use Definitely246\LoginToken\Models\LoginToken;

interface TokenDriverInterface
{
	public function attempt($tokenString);
	public function check($tokenString);
	public function generate($attachments = array());
	public function logout(LoginToken $token);
	public function refresh(LoginToken $token);
	public function token($tokenString = null);
	public function tokenString($request = null);
}