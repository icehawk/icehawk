<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

/**
 * Class InvalidEventSubscriberCollection
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class InvalidEventSubscriberCollection extends IceHawkException
{
	/** @var array */
	private $invalidKeys = [ ];

	/**
	 * @return array
	 */
	public function getInvalidKeys() : array
	{
		return $this->invalidKeys;
	}

	/**
	 * @param array $invalidKeys
	 *
	 * @return InvalidEventSubscriberCollection
	 */
	public function withInvalidKeys( array $invalidKeys ) : self
	{
		$this->invalidKeys = $invalidKeys;

		return $this;
	}
}
