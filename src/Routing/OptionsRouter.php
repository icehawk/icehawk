<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
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
	 * @return RoutesToHandler[]
	 */
	public function findMatchingRoutes( string $uri ) : array
	{
		$matchedRoutes = [];

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
