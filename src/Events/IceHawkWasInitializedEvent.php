<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesEventData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class IceHawkWasInitializedEvent
 *
 * @package Fortuneglobe\IceHawk\Events
 */
final class IceHawkWasInitializedEvent implements ServesEventData
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
	public function getRequestInfo()
	{
		return $this->requestInfo;
	}
}
