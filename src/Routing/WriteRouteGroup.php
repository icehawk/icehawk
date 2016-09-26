<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
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

use IceHawk\IceHawk\Interfaces\HandlesWriteRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;
use IceHawk\IceHawk\Routing\Patterns\NamedRegExp;

/**
 * Class WriteRouteGroup
 * @package IceHawk\IceHawk\Routing
 */
final class WriteRouteGroup implements RoutesToWriteHandler
{
	/** @var NamedRegExp */
	private $pattern;

	/** @var HandlesWriteRequest */
	private $requestHandler;

	/** @var array */
	private $routes;

	/** @var array */
	private $uriParams = [];

	public function __construct( ProvidesMatchResult $pattern, array $routes = [] )
	{
		$this->pattern = $pattern;
		$this->routes  = $routes;
	}

	public function addRoute( RoutesToWriteHandler $route ) : self
	{
		$this->routes[] = $route;

		return $this;
	}

	public function matches( string $uri ) : bool
	{
		if ( $this->pattern->matches( $uri ) )
		{
			foreach ( $this->routes as $route )
			{
				if ( $route->matches( $uri ) )
				{
					$this->requestHandler = $route->getRequestHandler();
					$this->uriParams      = $route->getUriParams();

					return true;
				}
			}
		}

		return false;
	}

	public function getUriParams() : array
	{
		return $this->uriParams;
	}

	/**
	 * @return HandlesWriteRequest|null
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}
}
