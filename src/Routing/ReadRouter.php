<?php declare(strict_types = 1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
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

		throw (new UnresolvedRequest())->withDestinationInfo( $destinationInfo );
	}
}
