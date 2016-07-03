<?php
namespace IceHawk\IceHawk\Events;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class InitializingIceHawkEvent
 * @package IceHawk\IceHawk\Events
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