<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Interface ConfiguresIceHawk
 * @package IceHawk\IceHawk\Interfaces
 */
interface ConfiguresIceHawk
{
	/**
	 * @return array|\Traversable|RoutesToReadHandler[]
	 */
	public function getReadRoutes();

	/**
	 * @return array|\Traversable|RoutesToWriteHandler[]
	 */
	public function getWriteRoutes();

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array;

	public function getRequestInfo() : ProvidesRequestInfo;

	public function getFinalReadResponder() : RespondsFinallyToReadRequest;

	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest;
}
