<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ResolvesWriteRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ResolvesWriteRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler;
}