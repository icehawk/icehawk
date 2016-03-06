<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub\Exceptions;

/**
 * Class EventSubscriberMethodNotCallable
 * @package Fortuneglobe\IceHawk\PubSub\Exceptions
 */
final class EventSubscriberMethodNotCallable extends PubSubException
{
	private $methodName = '';

	public function withMethodName( string $methodName ) : self
	{
		$this->methodName = $methodName;

		return $this;
	}

	public function getMethodName() : string
	{
		return $this->methodName;
	}
}