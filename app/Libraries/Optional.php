<?php namespace App\Libraries;

class Optional
{
	/**
	 * The target being transformed.
	 * Use _ prefix to avoid namespace conflict on __get()
	 *
	 * @var mixed
	 */
	protected $_target;

	/**
	 * Create a new transform proxy instance.
	 *
	 * @param mixed $target
	 */
	public function __construct($target)
	{
		$this->_target = $target;
	}

	/**
	 * Dynamically pass property fetching to the target when it's present.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		if (is_object($this->_target))
		{
			return $this->_target->{$property};
		}

		return null;
	}

	/**
	 * Dynamically pass method calls to the target when it's present.
	 *
	 * @param string $method
	 * @param array $parameters
	 *
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (is_object($this->_target))
		{
			return $this->_target->{$method}(...$parameters);
		}

		return null;
	}

	/**
	 * Allow optional(null)->present()->prop to return null without a decorated
	 * null dereference exception.
	 *
	 * @return Optional|mixed
	 */
	public function present()
	{
		if (is_object($this->_target))
		{
			return $this->_target->present(...func_get_args());
		}

		return new Optional(null);
	}
}
