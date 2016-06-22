<?php
namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class InitializingIceHawkEvent
 *
 * @package Fortuneglobe\IceHawk\Events
 */
final class InitializingIceHawkEvent implements CarriesEventData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/**
	 * @param ProvidesRequestInfo $requestInfo
	 */
	public function __construct( ProvidesRequestInfo $requestInfo )
	{
		$this->requestInfo = $requestInfo;
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}