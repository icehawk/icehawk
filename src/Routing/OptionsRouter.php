<?php
namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToHandler;

/**
 * Class OptionsRouter
 *
 * @package Fortuneglobe\IceHawk\Routing
 */
final class OptionsRouter extends AbstractRouter
{
	/**
	 * @param ProvidesRequestInfo $requestInfo
	 *
	 * @return RoutesToHandler[]
	 */
	public function findMatchingRoutes( string $uri ) : array
	{
		$matchedRoutes = [ ];

		foreach ( $this->getRoutes() as $route )
		{
			if ( $route->matches( $uri ) )
			{
				$matchedRoutes[] = $route;
			}
		}

		return $matchedRoutes;
	}
}