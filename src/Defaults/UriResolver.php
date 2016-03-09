<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\HandlerDemand;
use Fortuneglobe\IceHawk\Interfaces\ProvidesHandlerDemand;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;

/**
 * Class UriResolver
 * @package Fortuneglobe\IceHawk
 */
class UriResolver implements ResolvesUri
{
	/**
	 * @param ProvidesRequestInfo $requestInfo
	 *
	 * @throws MalformedRequestUri
	 * @return ProvidesHandlerDemand
	 */
	public function resolveUri( ProvidesRequestInfo $requestInfo ) : ProvidesHandlerDemand
	{
		$uri     = $requestInfo->getUri();
		$pattern = "#^\/([^\/\?\#]+)\/([^\/\?\#]+)\/?#";
		$matches = [ ];

		if ( preg_match( $pattern, $uri, $matches ) )
		{
			$domain = strtolower( $matches[1] );
			$demand = strtolower( $matches[2] );

			return new HandlerDemand( $domain, $demand, [ ] );
		}
		else
		{
			throw new MalformedRequestUri( $uri );
		}
	}
}