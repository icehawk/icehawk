<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class RequestWasHandledEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class RequestWasHandledEvent implements CarriesEventData
{
	/** @var ServesPostRequestData|ServesGetRequestData|ServesRequestData */
	private $request;

	/**
	 * @param ServesPostRequestData|ServesGetRequestData|ServesRequestData $request
	 */
	public function __construct( ServesRequestData $request )
	{
		$this->request = $request;
	}

	public function getRequestInfo() : ServesRequestInfo
	{
		return $this->request->getRequestInfo();
	}

	/**
	 * @return ServesGetRequestData|ServesPostRequestData|ServesRequestData
	 */
	public function getRequest() : ServesRequestData
	{
		return $this->request;
	}
}
