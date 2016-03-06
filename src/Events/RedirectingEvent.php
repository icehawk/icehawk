<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class RedirectingEvent
 * @package Fortuneglobe\IceHawk\Events
 */
final class RedirectingEvent implements CarriesEventData
{
	/** @var Redirect */
	private $redirect;

	/** @var ServesRequestInfo */
	private $requestInfo;

	public function __construct( Redirect $redirect, ServesRequestInfo $requestInfo )
	{
		$this->redirect    = $redirect;
		$this->requestInfo = $requestInfo;
	}

	/**
	 * @return Redirect
	 */
	public function getRedirect()
	{
		return $this->redirect;
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return $this->requestInfo;
	}
}