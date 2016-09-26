<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Events;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class IceHawkWasInitializedEvent
 * @package IceHawk\IceHawk\Events
 */
final class IceHawkWasInitializedEvent implements CarriesEventData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	public function __construct( ProvidesRequestInfo $requestInfo )
	{
		$this->requestInfo = $requestInfo;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}
