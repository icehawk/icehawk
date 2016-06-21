<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Routing\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;

/**
 * Interface RoutesToWriteHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToWriteHandler extends RoutesToHandler
{
	/**
	 * @return HandlesWriteRequest
	 */
	public function getRequestHandler();
}