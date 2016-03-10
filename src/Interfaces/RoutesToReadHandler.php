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
	public function getRequestHandler() : HandlesReadRequest;
}