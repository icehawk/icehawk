<?php
namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Routing\Interfaces\RoutesToHandler;

/**
 * Class OptionsRouter
 * @package IceHawk\IceHawk\Routing
 */
final class OptionsRouter extends AbstractRouter
{
	/**
	 * @param string $uri
	 * 
*@return RoutesToHandler[]
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