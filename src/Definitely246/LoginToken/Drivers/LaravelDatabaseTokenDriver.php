<?php namespace Definitely246\LoginToken\Drivers;

use DateTime,
	Definitely246\LoginToken\Models\LoginToken,
	Definitely246\LoginToken\Interfaces\TokenDriverInterface,
	Definitely246\LoginToken\Exceptions\EmptyTokenException,
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
	 * [$currentToken description]
	 * @var [type]
	 */
	protected $currentToken;

	/**
	 * [$router description]
	 * @var [type]
	 */
	protected $router;

	/**
	 * Initialize the database token driver 
	 * 
	 * @param [type] $database
	 */
	public function __construct($app)
	{
		$this->currentToken = null;
		$this->router = $app['router'];
		$this->table = $app['db']->table('def246_login_tokens');
		$this->token = new LoginToken(array(), $this);
		$this->tokenString = $app['config']->get('login-token::config.token_string');
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
		if (!$tokenString)
		{
			throw new EmptyTokenException("Cannot look up a token from empty token string.");
		}

		$token = $this->token->find($tokenString);

		if (!$token)
		{
			throw new InvalidTokenException("This token string could not be found!");
		}

		if ($token->isExpired())
		{
			throw new ExpiredTokenException("This token string has expired!", $token);
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

		if ($results && isset($results->attachments) && $results->attachments !== null)
		{
			$results->attachments = unserialize($results->attachments);
		}

		return $results ? $this->token->newInstance((array) $results, $this) : null;
	}

	/**
	 * Generates a new token
	 * 
	 * @param  int    $identifiable_id
	 * @param  string $identifiable_type
	 * @param  string $expires_at
	 * @return LoginToken
	 */
	public function generate($expires_at = null, $attachments = array())
	{
		$token = $this->token->newInstance();

		do {
			$hashkey = $this->randomString(240);
			$found = $token->find($hashkey);
		} while ($found);

		$token->expires_at = $expires_at;
		$token->attachments = $attachments;
		$token->token_string = $hashkey;
		$token->created_at = new DateTime;
		$token->updated_at = new DateTime;

		$token->save();

		return $token;
	}

	/**
	 * Returns a login token if one is active at the moment
	 * 
	 * @return LoginToken
	 */
	public function token($tokenString = null)
	{
		$tokenString = $tokenString ?: $this->tokenString();

		if ($tokenString)
		{
			return $this->check($tokenString);
		}

		return null;
	}

	/**
	 * Returns the token string for this request
	 * 
	 * @param  $request
	 * @return 
	 */
	public function tokenString($request = null)
	{
		$request = $request ?: $this->router->getCurrentRequest();
		return call_user_func_array($this->tokenString, array($request));
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
		$attributes = $token->getAttributes();

		if (array_key_exists('attachments', $attributes))
		{
			$attributes['attachments'] = serialize($attributes['attachments']);
		} 

		if ($token->id)
		{
			return $this->table->where('id', $token->id)->update($attributes);
		}

		$token->id = $this->table->insertGetId($attributes);

		return $token;
	}

	/**
	 * Generates a random string for us
	 * 
	 * @param  integer $length [description]
	 * @param  string  $pool   [description]
	 * @return [type]          [description]
	 */
	private function randomString($length = 16, $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}

	/**
	 * Get the current token given a route and request
	 * 
	 * @param  $route
	 * @param  $request
	 * @return LoginToken
	 */
	private function currentToken($route, $request)
	{
		$attributes = array();
		return new LoginToken($attributes, $this);		
	}
}