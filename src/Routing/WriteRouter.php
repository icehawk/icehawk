<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Constants\HandlerMethodInterfaceMap;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class WriteRouter
 * @package IceHawk\IceHawk\Routing
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