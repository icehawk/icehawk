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
	 * @param ServesRequestInfo $request_info
	 *
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	public function resolveUri( ServesRequestInfo $request_info )
	{
		$uri     = $request_info->getUri();
		$pattern = "#^\/(?:([^\/\?\#]+)\/)?(?:v([0-9\.]+)\/)?([^\/\?\#]+)\/([^\/\?\#]+)\/?#";
		$matches = [ ];

		if ( preg_match( $pattern, $uri, $matches ) )
		{
			$api         = ($matches[1] !== '') ? strtolower( $matches[1] ) : Api::COMMON;
			$api_version = $matches[2] ?: Api::VERSION_DEFAULT;
			$domain      = strtolower( $matches[3] );
			$command     = strtolower( $matches[4] );

			return new UriComponents( $api, $api_version, $domain, $command );
		}
		else
		{
			throw new MalformedRequestUri( $uri );
		}
	}
}