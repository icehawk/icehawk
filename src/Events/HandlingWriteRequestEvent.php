<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class HandlingWriteRequestEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class HandlingWriteRequestEvent implements CarriesEventData
{

	/** @var ProvidesWriteRequestData */
	private $request;

	public function __construct( ProvidesWriteRequestData $request )
	{
		$this->request = $request;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}

	public function getRequest() : ProvidesWriteRequestData
	{
		return $this->request;
	}
}
