<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface BuildsDomainRequestHandlers
{
	/**
	 * @param ServesUriComponents $uriComponents
	 * @param ServesRequestData   $request
	 *
	 * @return HandlesDomainRequests
	 */
	public function buildDomainRequestHandler( ServesUriComponents $uriComponents, ServesRequestData $request );
}