<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class UriRewriter
 *
 * @package Fortuneglobe\IceHawk
 */
class UriRewriter implements RewritesUri
{
	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @return Redirect
	 */
	public function rewrite( ServesRequestInfo $requestInfo )
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), [ ] );
	}

	/**
	 * @param string $requestUri
	 * @param array  $simpleMap
	 *
	 * @return Redirect
	 */
	protected function rewriteUriBySimpleMap( $requestUri, array $simpleMap )
	{
		$redirect = $this->buildRedirect( $requestUri, Http::MOVED_PERMANENTLY );

		foreach ( $simpleMap as $pattern => list( $redirectUri, $redirectCode ) )
		{
			if ( $this->uriMatchesPattern( $requestUri, $pattern ) )
			{
				$redirect = $this->buildRedirect( $redirectUri, $redirectCode );
				break;
			}
		}

		return $redirect;
	}

	/**
	 * @param string $requestUri
	 * @param string $pattern
	 *
	 * @return bool
	 */
	private function uriMatchesPattern( $requestUri, $pattern )
	{
		if ( $requestUri == $pattern )
		{
			return true;
		}
		elseif ( @preg_match( $pattern, '' ) !== false )
		{
			return boolval( preg_match( $pattern, $requestUri ) );
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string      $redirectUri
	 * @param string|null $redirectCode
	 *
	 * @return Redirect
	 */
	private function buildRedirect( $redirectUri, $redirectCode )
	{
		if ( is_null( $redirectCode ) )
		{
			return new Redirect( $redirectUri );
		}
		else
		{
			return new Redirect( $redirectUri, $redirectCode );
		}
	}
}