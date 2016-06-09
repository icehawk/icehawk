<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RoutesToHandler
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RoutesToHandler
{
	public function matches( ProvidesRequestInfo $requestInfo );

	public function getUriParams() : array;
}