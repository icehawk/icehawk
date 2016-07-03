<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Routing\Interfaces;

use IceHawk\IceHawk\Interfaces\HandlesReadRequest;

/**
 * Interface RoutesToReadHandler
 * @package IceHawk\IceHawk\Interfaces
 */
interface RoutesToReadHandler extends RoutesToHandler
{
	/**
	 * @return HandlesReadRequest
	 */
	public function getRequestHandler();
}