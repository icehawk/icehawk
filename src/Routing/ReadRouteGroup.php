<?php
namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToReadHandler;

/**
 * Class ReadRouteGroup
 * @package Fortuneglobe\IceHawk\Routing
 */
final class ReadRouteGroup implements RoutesToReadHandler
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var HandlesReadRequest */
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

	public function addRoute( RoutesToReadHandler $route ) : self
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
	 * @return HandlesReadRequest|null
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}
}