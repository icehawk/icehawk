<?php
namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Interfaces\HandlesWriteRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;
use IceHawk\IceHawk\Routing\Patterns\NamedRegExp;

/**
 * Class WriteRouteGroup
 * @package IceHawk\IceHawk\Routing
 */
final class WriteRouteGroup implements RoutesToWriteHandler
{
	/** @var NamedRegExp */
	private $pattern;

	/** @var HandlesWriteRequest */
	private $requestHandler;

	/** @var array */
	private $routes;

	/** @var array */
	private $uriParams = [ ];

	public function __construct( ProvidesMatchResult $pattern, array $routes = [ ] )
	{
		$this->pattern = $pattern;
		$this->routes  = $routes;
	}

	public function addRoute( RoutesToWriteHandler $route ) : self
	{
		$this->routes[] = $route;

		return $this;
	}

	public function matches( string $uri ) : bool
	{
		if ( $this->pattern->matches( $uri ) )
		{
			foreach ( $this->routes as $route )
			{
				if ( $route->matches( $uri ) )
				{
					$this->requestHandler = $route->getRequestHandler();
					$this->uriParams      = $route->getUriParams();

					return true;
				}
			}
		}

		return false;
	}

	public function getUriParams() : array
	{
		return $this->uriParams;
	}

	/**
	 * @return HandlesWriteRequest|null
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}
}