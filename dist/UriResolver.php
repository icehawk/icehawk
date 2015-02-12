<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class UriResolver
 *
 * @package Fortuneglobe\IceHawk
 */
class UriResolver implements ResolvesUri
{
	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	public function resolveUri( ServesRequestInfo $requestInfo )
	{
		$uri     = $requestInfo->getUri();
		$pattern = "#^\/([^\/\?\#]+)\/([^\/\?\#]+)\/?#";
		$matches = [ ];

		if ( preg_match( $pattern, $uri, $matches ) )
		{
			$domain = strtolower( $matches[1] );
			$demand = strtolower( $matches[2] );

			return new UriComponents( $domain, $demand );
		}
		else
		{
			throw new MalformedRequestUri( $uri );
		}
	}
}