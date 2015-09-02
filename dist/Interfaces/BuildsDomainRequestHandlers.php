<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsDomainRequestHandlers
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsDomainRequestHandlers
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @return HandlesDomainRequests
	 */
	public function buildDomainRequestHandler( ServesRequestData $request );
}
