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
	 * Helper for attachments
	 * 
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getAttachment($name)
	{
		$attachments = $this->getAttribute('attachments');

		if (is_array($attachments) && array_key_exists($name, $attachments))
		{
			return $attachments[$name];			
		}

		return null;
	}
}