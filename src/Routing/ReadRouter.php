<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;

/**
 * Class ReadRouter
 * @package Fortuneglobe\IceHawk\Routing
 */
final class ReadRouter extends AbstractRouter
{
	/**
	 * @param ProvidesRequestInfo $requestInfo
	 *
	 * @throws UnresolvedRequest
	 * @return RoutesToReadHandler
	 */
	public function findMatchingRoute( ProvidesRequestInfo $requestInfo ) : RoutesToReadHandler
	{
		foreach ( $this->getRoutes() as $route )
		{
			if ( !($route instanceof RoutesToReadHandler) )
			{
				continue;
			}

			if ( $route->matches( $requestInfo ) )
			{
				return $route;
			}
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}