<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Routing\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Interface RoutesToHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToHandler
{
	public function matches( ProvidesRequestInfo $requestInfo ) : bool;

	public function getUriParams() : array;
}