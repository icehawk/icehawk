<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Interface ConfiguresIceHawk
 * @package Fortuneglobe\IceHawk\Interfaces
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
