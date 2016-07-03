<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestInputData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class HandlingReadRequestEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class HandlingReadRequestEvent implements CarriesEventData
{
	/** @var ProvidesReadRequestData */
	private $request;

	public function __construct( ProvidesReadRequestData $request )
	{
		$this->request = $request;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}

	public function getInputData() : ProvidesReadRequestInputData
	{
		return $this->request->getInputData();
	}
}
