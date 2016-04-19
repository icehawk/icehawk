<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Events;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
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

	/** @var ProvidesRequestInfo */
	private $requestInfo;

	public function __construct( Redirect $redirect, ProvidesRequestInfo $requestInfo )
	{
		$this->redirect    = $redirect;
		$this->requestInfo = $requestInfo;
	}

	/**
	 * @return Redirect
	 */
	public function getRedirect() : Redirect
	{
		return $this->redirect;
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}