<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Events;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestInputData;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class HandlingWriteRequestEvent
 * @package IceHawk\IceHawk\Events
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
		return $this->request->getInfo();
	}

	public function getRequestInput() : ProvidesWriteRequestInputData
	{
		return $this->request->getInput();
	}
}
