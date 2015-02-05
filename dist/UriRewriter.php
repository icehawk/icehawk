<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class UriRewriter
 *
 * @package Fortuneglobe\IceHawk
 */
class UriRewriter implements RewritesUri
{
	/**
	 * @param ServesRequestInfo $request_info
	 *
	 * @return string
	 */
	public function rewrite( ServesRequestInfo $request_info )
	{
		return $request_info->getUri();
	}

	/**
	 * @param string $request_uri
	 * @param array  $simple_map
	 *
	 * @return string
	 */
	protected function rewriteUriBySimpleMap( $request_uri, array $simple_map )
	{
		$mapped_uri = $request_uri;

		foreach ( $simple_map as $pattern => $uri )
		{
			if ( $this->uriMatchesPattern( $request_uri, $pattern ) )
			{
				$mapped_uri = $uri;
				break;
			}
		}

		return $mapped_uri;
	}

	/**
	 * @param string $request_uri
	 * @param string $pattern
	 *
	 * @return bool
	 */
	private function uriMatchesPattern( $request_uri, $pattern )
	{
		if ( $request_uri == $pattern )
		{
			return true;
		}
		elseif ( @preg_match( $pattern, '' ) !== false )
		{
			return boolval( preg_match( $pattern, $request_uri ) );
		}
		else
		{
			return false;
		}
	}
}