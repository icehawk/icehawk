<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class WriteRouter
 *
 * @package Fortuneglobe\IceHawk\Routing
 */
final class WriteRouter extends AbstractRouter
{
	/**
	 * @param ProvidesDestinationInfo $destinationInfo
	 *
	 * @throws UnresolvedRequest
	 * @return RoutesToWriteHandler
	 */
	public function findMatchingRoute( ProvidesDestinationInfo $destinationInfo ) : RoutesToWriteHandler
	{		
		$requiredHandlerType = HandlerMethodInterfaceMap::HTTP_METHODS[ $destinationInfo->getRequestMethod() ];
		$uri = $destinationInfo->getUri();

		foreach ( $this->getRoutes() as $route )
		{
			if ( !($route instanceof RoutesToWriteHandler) )
			{
				continue;
			}

			if ( $route->matches( $uri ) && $route->getRequestHandler() instanceof $requiredHandlerType )
			{
				return $route;
			}
		}

		throw ( new UnresolvedRequest() )->withDestinationInfo( $destinationInfo );
	}
}