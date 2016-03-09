<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;

/**
 * Interface HandlesDomainRequests
 * @package Fortuneglobe\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesRequest
{
	public function handleRequest( ProvidesRequestData $request );
}