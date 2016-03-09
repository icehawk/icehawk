<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class HandlingRequestEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class HandlingRequestEvent implements CarriesEventData
{

	/** @var ProvidesWriteRequestData|ProvidesReadRequestData|ProvidesRequestData */
	private $request;

	/**
	 * @param ProvidesWriteRequestData|ProvidesReadRequestData|ProvidesRequestData $request
	 */
	public function __construct( ProvidesRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}

	/**
	 * @return ProvidesReadRequestData|ProvidesWriteRequestData|ProvidesRequestData
	 */
	public function getRequest() : ProvidesRequestData
	{
		return $this->request;
	}
}
