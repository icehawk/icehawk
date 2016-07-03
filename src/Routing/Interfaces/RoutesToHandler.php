<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Routing\Interfaces;

/**
 * Interface RoutesToHandler
 * @package IceHawk\IceHawk\Interfaces
 */
interface RoutesToHandler
{
	public function matches( string $uri ) : bool;

	public function getUriParams() : array;
}