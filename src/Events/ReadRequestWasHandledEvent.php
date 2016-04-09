<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class RequestWasHandledEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class ReadRequestWasHandledEvent implements CarriesEventData
{
	/** @var ProvidesReadRequestData */
	private $request;

	/**
	 * @param ProvidesReadRequestData $request
	 */
	public function __construct( ProvidesReadRequestData $request )
	{
		$this->request = $request;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}

	/**
	 * @return ProvidesReadRequestData
	 */
	public function getRequest() : ProvidesReadRequestData
	{
		return $this->request;
	}
}
