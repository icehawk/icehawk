<?php
namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class ReadRouteRedirect
 *
 * @package IceHawk\IceHawk\Routing
 */
final class RouteRedirect implements RedirectsRoute
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var  string */
	private $finalUri;

	/** @var  string */
	private $finalMethod;

	/** @var array */
	private $uriParams = [];

	public function __construct( ProvidesMatchResult $pattern, string $finalUri, string $finalMethod )
	{
		$this->pattern     = $pattern;
		$this->finalUri    = $finalUri;
		$this->finalMethod = $finalMethod;
	}

	public function matches( string $uri ) : bool
	{
		if ( $this->pattern->matches( $uri ) )
		{
			$this->uriParams = $this->pattern->getMatches();

			return true;
		}

		return false;
	}

	public function getFinalUri() : string
	{
		return $this->finalUri;
	}

	public function getFinalMethod() : string
	{
		return $this->finalMethod;
	}

	/**
	 * @return array
	 */
	public function getUriParams() : array
	{
		return $this->uriParams;
	}
}