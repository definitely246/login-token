<?php namespace Definitely246\LoginToken\Models;

use ReflectionClass;

abstract class AbstractModel
{
	/**
	 * Holds all attributes in this model
	 * @var [type]
	 */
	protected $attributes;

	/**
	 * Holds the driver used to save and find this model
	 * @var [type]
	 */
	protected $driver;

	/**
	 * The model can 
	 * @param [type] $driver [description]
	 */
	public function __construct($attributes = array(), $driver = null)
	{
		$this->attributes = $attributes;
		$this->driver = $driver;
	}

	/**
	 * Shortcut to fill up the attributes on this model
	 * 
	 * @param  array $attributes
	 * @return $this
	 */
	public function fill($attributes)
	{
		foreach ($attributes as $attributeKey => $attributeValue)
		{
			$this->attributes[$attributeKey] = $attributeValue;
		}

		return $this;
	}

	/**
	 * Find by id using given driver
	 * 
	 * @param  <any> $id
	 * @return Model
	 */
	public function find($id)
	{
		return $this->driver->check($id);
	}

	/**
	 * Save this model using the driver given
	 * 
	 * @return void
	 */
	public function save()
	{
		return $this->driver->refresh($this);
	}

	/**
	 * Remove this model using the driver given
	 * 
	 * @return void
	 */
	public function delete()
	{
		return $this->driver->logout($this);
	}

	/**
	 * Return the attribute with the given name
	 * 
	 * @param  string $name
	 * @return <any>
	 */
	public function getAttribute($name)
	{
		if (array_key_exists($name, $this->attributes))
		{
			return $this->attributes[$name];
		}
		
		return null;
	}

	/**
	 * Set the attribute with the given name
	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * Return all attributes in the $attributes array
	 * 
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Set all attributes at once
	 * 
	 * @return array
	 */
	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * Attribute does not exist on class
	 * so try and fetch it from $attributes
	 * 
	 * @param  string $name
	 * @return <any>
	 */
	public function __get($name)
	{
		return $this->getAttribute($name);
	}

	/**
	 * Attribute does not exist on class
	 * so set it in $attributes array
	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name, $value)
	{
		$this->setAttribute($name, $value);
	}

	/**
	 * Calls a constructor for this model
	 * 
	 * @return new static($args)
	 */
	public function newInstance($attributes = array(), $driver = null)
	{
		$driver = $driver ?: $this->driver;

		return new static($attributes, $driver);
	}
}