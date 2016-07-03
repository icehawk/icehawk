<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Routing\Interfaces;

use IceHawk\IceHawk\Interfaces\HandlesWriteRequest;

/**
 * Interface RoutesToWriteHandler
 * @package IceHawk\IceHawk\Interfaces
 */
interface RoutesToWriteHandler extends RoutesToHandler
{
	/**
	 * @return HandlesWriteRequest
	 */
	public function getRequestHandler();
}