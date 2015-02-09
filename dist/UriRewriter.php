<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

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
	 * @param ServesRequestInfo $request_info
	 *
	 * @return Redirect
	 */
	public function rewrite( ServesRequestInfo $request_info )
	{
		return $this->rewriteUriBySimpleMap( $request_info, [ ] );
	}

	/**
	 * @param string $request_uri
	 * @param array  $simple_map
	 *
	 * @return Redirect
	 */
	protected function rewriteUriBySimpleMap( $request_uri, array $simple_map )
	{
		$redirect = new Redirect( $request_uri );

		foreach ( $simple_map as $pattern => list( $redirect_uri, $redirect_code ) )
		{
			if ( $this->uriMatchesPattern( $request_uri, $pattern ) )
			{
				$redirect = $this->buildRedirect( $redirect_uri, $redirect_code );
				break;
			}
		}

		return $redirect;
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

	/**
	 * @param string      $redirect_uri
	 * @param string|null $redirect_code
	 *
	 * @return Redirect
	 */
	private function buildRedirect( $redirect_uri, $redirect_code )
	{
		if ( is_null( $redirect_code ) )
		{
			return new Redirect( $redirect_uri );
		}
		else
		{
			return new Redirect( $redirect_uri, $redirect_code );
		}
	}
}