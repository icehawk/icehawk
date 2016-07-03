<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Routing\Interfaces;

/**
 * Interface RoutesToHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToHandler
{
	public function matches( string $uri ) : bool;

	public function getUriParams() : array;
}