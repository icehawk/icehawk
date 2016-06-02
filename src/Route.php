<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class Route
 *
 * @package Fortuneglobe\IceHawk
 */
class Route
{
	/** @var RequestMethod */
	private $requestMethod;

	/** @var string */
	private $uriPattern;

	/** @var HandlesRequest */
	private $requestHandler;

	/**
	 * @param RequestMethod  $requestMethod
	 * @param string         $uriPattern
	 * @param HandlesRequest $requestHandler
	 */
	public function __construct( RequestMethod $requestMethod, string $uriPattern, HandlesRequest $requestHandler )
	{
		$this->requestMethod  = $requestMethod;
		$this->uriPattern     = $uriPattern;
		$this->requestHandler = $requestHandler;
	}

	public function matches( ProvidesRequestInfo $requestInfo ) : bool
	{
		if ( $requestInfo->getMethod() == $this->requestMethod->toString() )
		{
			$quotedPattern = preg_quote( $this->uriPattern );

			return (bool)preg_match( $quotedPattern, $requestInfo->getUri() );
		}

		return false;
	}

	/**
	 * @return HandlesRequest
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}
}

class Router
{
	/** @var Route[] */
	private $routes;

	public function add( Route $route )
	{
		if ( !in_array( $route, $this->routes ) )
		{
			$this->routes[] = $route;
		}
	}

	public function resolveRequestHandler( ProvidesRequestInfo $requestInfo ) : HandlesRequest
	{
		foreach ( $this->routes as $route )
		{
			if ( $route->matches( $requestInfo ) )
			{
				return $route->getRequestHandler();
			}
		}

		throw new \Exception( 'Unble to resolve request' );
	}
}
