<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\MissingRedirectUrlInRewriteMap;
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
		$redirect = $this->buildRedirect( $requestUri, null );

		foreach ( $simpleMap as $pattern => $redirectData )
		{
			if ( $this->uriMatchesPattern( $requestUri, $pattern ) )
			{
				$this->guardRedirectUrlIsSetInMap( $redirectData );

				$redirectUrl  = $redirectData[0];
				$redirectCode = isset($redirectData[1]) ? $redirectData[1] : null;

				$redirect = $this->buildRedirect( $redirectUrl, $redirectCode );
				break;
			}
		}

		return $redirect;
	}

	/**
	 * @param mixed $redirectData
	 *
	 * @throws MissingRedirectUrlInRewriteMap
	 */
	private function guardRedirectUrlIsSetInMap( $redirectData )
	{
		if ( !is_array( $redirectData ) )
		{
			throw new MissingRedirectUrlInRewriteMap( 'Redirect data is not an array.' );
		}
		elseif ( !isset($redirectData[0]) )
		{
			throw new MissingRedirectUrlInRewriteMap();
		}
	}

	/**
	 * @param string $requestUrl
	 * @param string $pattern
	 *
	 * @return bool
	 */
	private function uriMatchesPattern( $requestUrl, $pattern )
	{
		if ( $requestUrl == $pattern )
		{
			return true;
		}
		elseif ( @preg_match( $pattern, '' ) !== false )
		{
			return boolval( preg_match( $pattern, $requestUrl ) );
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string $redirectUrl
	 * @param string|null $redirectCode
	 *
	 * @return Redirect
	 */
	private function buildRedirect( $redirectUrl, $redirectCode )
	{
		if ( is_null( $redirectCode ) )
		{
			return new Redirect( $redirectUrl );
		}
		else
		{
			return new Redirect( $redirectUrl, $redirectCode );
		}
	}
}