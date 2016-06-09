<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;

/**
 * Class WriteRouter
 * @package Fortuneglobe\IceHawk\Routing
 */
final class WriteRouter extends AbstractRouter
{
	/**
	 * @param ProvidesRequestInfo $requestInfo
	 *
	 * @throws UnresolvedRequest
	 * @return RoutesToWriteHandler
	 */
	public function findMatchingRoute( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler
	{
		foreach ( $this->getRoutes() as $route )
		{
			if ( !($route instanceof RoutesToWriteHandler) )
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