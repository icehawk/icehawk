<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class UncaughtExceptionWasThrownEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class UncaughtExceptionWasThrownEvent implements CarriesEventData
{
	/** @var \Throwable */
	private $throwable;

	/** @var ServesRequestInfo */
	private $requestInfo;

	public function __construct( \Throwable $throwable, ServesRequestInfo $requestInfo )
	{
		$this->throwable   = $throwable;
		$this->requestInfo = $requestInfo;
	}

	public function getThrowable() : \Throwable
	{
		return $this->throwable;
	}

	public function getRequestInfo() : ServesRequestInfo
	{
		return $this->requestInfo;
	}
}