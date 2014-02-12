<?php namespace Definitely246\LoginToken\Models;

use DateTime;

class LoginToken extends AbstractModel
{
	/**
	 * Is this token expired?
	 * 
	 * @return boolean [description]
	 */
	public function isExpired()
	{
		return $this->expires_at && new DateTime($this->expires_at) < new DateTime;
	}

	/**
	 * Generate a new token
	 * 
	 * @param  int    $identifiable_id
	 * @param  string $identifiable_type
	 * @param  string $expires_at
	 * @return LoginToken
	 */
	public function generate($identifiable_id = null, $identifiable_type = null, $expires_at = null)
	{
		do {
			$tokenString = $this->randomString(240);
			$found = $this->find($tokenString);
		} while ($found);

		$this->identifiable_id = $identifiable_id;
		$this->identifiable_type = $identifiable_type;
		$this->expires_at = $expires_at;
		$this->token_string = $tokenString;
		$this->created_at = new DateTime;
		$this->updated_at = new DateTime;
		$this->save();

		return $this;
	}

	/**
	 * [getIdentifier description]
	 * @return [type] [description]
	 */
	public function getIdentifier()
	{
		if ($this->identifiable_type && $this->identifiable_id)
		{
			$constructor = new ReflectionClass($this->identifiable_type);
			$instance = $constructor->newInstance();
			return $instance->find($this->identifiable_id);
		}

		return null;
	}
}