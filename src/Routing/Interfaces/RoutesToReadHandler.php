<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Routing\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;

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