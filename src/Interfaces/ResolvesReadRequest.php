<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ResolvesReadRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ResolvesReadRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToReadHandler;
}