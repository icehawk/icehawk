<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class IceHawkWasInitializedEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class IceHawkWasInitializedEvent implements CarriesEventData
{
	/** @var ServesRequestInfo */
	private $requestInfo;

	/**
	 * @param ServesRequestInfo $requestInfo
	 */
	public function __construct( ServesRequestInfo $requestInfo )
	{
		$this->requestInfo = $requestInfo;
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo() : ServesRequestInfo
	{
		return $this->requestInfo;
	}
}
