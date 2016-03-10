<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RoutesToWriteHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToWriteHandler
{
	public function getRequestHandler() : HandlesWriteRequest;
}