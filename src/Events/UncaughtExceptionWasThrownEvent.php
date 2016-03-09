<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class UncaughtExceptionWasThrownEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class UncaughtExceptionWasThrownEvent implements CarriesEventData
{
	/** @var \Throwable */
	private $throwable;

	/** @var ProvidesRequestInfo */
	private $requestInfo;

	public function __construct( \Throwable $throwable, ProvidesRequestInfo $requestInfo )
	{
		$this->throwable   = $throwable;
		$this->requestInfo = $requestInfo;
	}

	public function getThrowable() : \Throwable
	{
		return $this->throwable;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}