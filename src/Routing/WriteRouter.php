<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class WriteRouter
 *
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
		$requiredHandlerType = HandlerMethodInterfaceMap::HTTP_METHODS[ $requestInfo->getMethod() ];

		foreach ( $this->getRoutes() as $route )
		{
			if ( !($route instanceof RoutesToWriteHandler) )
			{
				continue;
			}

			if ( $route->matches( $requestInfo ) && $route->getRequestHandler() instanceof $requiredHandlerType )
			{
				return $route;
			}
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}