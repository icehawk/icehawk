<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

/**
 * Class InvalidEventListenerCollection
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class InvalidEventListenerCollection extends IceHawkException
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
	 * @return InvalidEventListenerCollection
	 */
	public function withInvalidKeys( array $invalidKeys ) : self
	{
		$this->invalidKeys = $invalidKeys;

		return $this;
	}
}
