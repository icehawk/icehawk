<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsDomainRequestHandlers
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsDomainRequestHandlers
{
	public function buildDomainRequestHandler( ServesRequestData $request ) : HandlesDomainRequests;
}
