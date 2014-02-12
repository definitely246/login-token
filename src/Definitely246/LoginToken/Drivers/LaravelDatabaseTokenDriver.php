<?php namespace Definitely246\LoginToken\Drivers;

use Schema, 
	DateTime,
	Definitely246\LoginToken\Models\LoginToken,
	Definitely246\LoginToken\Interfaces\TokenDriverInterface,
	Definitely246\LoginToken\Exceptions\InvalidTokenException,
	Definitely246\LoginToken\Exceptions\ExpiredTokenException;

class LaravelDatabaseTokenDriver implements TokenDriverInterface
{
	/**
	 * [$token description]
	 * @var [type]
	 */
	protected $token;

	/**
	 * [$table description]
	 * @var [type]
	 */
	protected $table; 

	/**
	 * Initialize the database token driver 
	 * 
	 * @param [type] $database
	 */
	public function __construct($app)
	{
		$this->token = new LoginToken(array(), $this);
		$this->table = $app['db']->table('def246_login_tokens');
	}

	/**
	 * Attempt to use this token to login.
	 * 
	 * @param  string $token
	 * @throws InvalidTokenStringException If token is not found or expired
	 * 
	 * @return Definitely246\LoginToken\Models\LoginToken
	 */
	public function attempt($tokenString)
	{
		$token = $this->token->find($tokenString);

		if (!$token) {
			throw new InvalidTokenException("This token string could not be found!");
		}

		if ($token->isExpired())
		{
			throw new ExpiredTokenException("This token string has expired!");
		}

		return $token;
	}

	/**
	 * Find the login token
	 * 
	 * @param  [type] $token
	 * @return [type]       
	 */
	public function check($tokenString)
	{
		$results = $this->table->where('token_string', '=', $tokenString)->first();

		return $results ? $this->token->newInstance((array) $results, $this) : null;
	}

	/**
	 * Returns a login token if one is active at the moment
	 * 
	 * @return LoginToken
	 */
	public function current()
	{
		return $this->token;
	}

	/**
	 * Logs a token out (just another way to remove a token really)
	 * 
	 * @param  LoginToken $token
	 * @return void
	 */
	public function logout(LoginToken $token)
	{
		return $this->table->where('id', '=', $token->id)->delete();
	}

	/**
	 * Persist the login token
	 * 
	 * @param  LoginToken $token
	 * @return bool
	 */
	public function refresh(LoginToken $token)
	{
		if ($token->id)
		{
			return $this->table->where('id', $token->id)->update($token->getAttributes());
		}

		$token->id = $this->table->insertGetId($token->getAttributes());

		return $token;
	}
}