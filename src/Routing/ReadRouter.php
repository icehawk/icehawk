<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Constants\HandlerMethodInterfaceMap;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToReadHandler;

/**
 * Class ReadRouter
 * @package IceHawk\IceHawk\Routing
 */
final class ReadRouter extends AbstractRouter
{
	/**
	 * @param ProvidesDestinationInfo $destinationInfo
	 *
	 * @throws UnresolvedRequest
	 * @return RoutesToReadHandler
	 */
	public function findMatchingRoute( ProvidesDestinationInfo $destinationInfo ) : RoutesToReadHandler
	{
		$requiredHandlerType = HandlerMethodInterfaceMap::HTTP_METHODS[ $destinationInfo->getRequestMethod() ];
		$uri                 = $destinationInfo->getUri();

		foreach ( $this->getRoutes() as $route )
		{
			if ( !($route instanceof RoutesToReadHandler) )
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