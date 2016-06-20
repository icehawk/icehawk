<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RoutesToReadHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToReadHandler extends RoutesToHandler
{
	/**
	 * @return HandlesReadRequest
	 */
	public function getRequestHandler();
}