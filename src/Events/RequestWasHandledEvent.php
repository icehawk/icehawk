<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesEventData;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class RequestWasHandledEvent
 *
 * @package Fortuneglobe\IceHawk\Events
 */
final class RequestWasHandledEvent implements ServesEventData
{
	/** @var ServesPostRequestData|ServesGetRequestData */
	private $request;

	/**
	 * @param ServesRequestData $request
	 */
	public function __construct( ServesRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return $this->request->getRequestInfo();
	}

	/**
	 * @return ServesGetRequestData|ServesPostRequestData
	 */
	public function getRequest()
	{
		return $this->request;
	}
}
