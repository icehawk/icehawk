<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesDomainRequests
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesDomainRequests
{
	/**
	 * @param ServesRequestData $request
	 */
	public function handleRequest( ServesRequestData $request );
}