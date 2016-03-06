<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\MissingRedirectUrlInRewriteMap;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class AbstractUriRewriter
 * @package Fortuneglobe\IceHawk
 */
abstract class AbstractUriRewriter implements RewritesUri
{
	/**
	 * @param string $requestUri
	 * @param array  $simpleMap
	 *
	 * @return Redirect
	 */
	protected function rewriteUriBySimpleMap( string $requestUri, array $simpleMap ) : Redirect
	{
		$redirect = $this->buildRedirect( $requestUri, null );

		foreach ( $simpleMap as $pattern => $redirectData )
		{
			if ( $this->uriMatchesPattern( $requestUri, $pattern ) )
			{
				$this->guardRedirectUrlIsSetInMap( $redirectData );

				$redirectUrl  = preg_replace( $pattern, $redirectData[0], $requestUri );
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
	 * @param string      $redirectUrl
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