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
 * Class WriteRequestWasHandledEvent
 * @package IceHawk\IceHawk\Events
 */
final class WriteRequestWasHandledEvent implements CarriesEventData
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

	public function getInputData() : ProvidesWriteRequestInputData
	{
		return $this->request->getInputData();
	}
}
