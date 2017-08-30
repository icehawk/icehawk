<?php declare(strict_types=1);
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

use IceHawk\IceHawk\Routing\Exceptions\RoutesAreNotTraversable;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToHandler;

/**
 * Class AbstractRouter
 * @package IceHawk\IceHawk\Routing
 */
abstract class AbstractRouter
{
	/** @var array|\Traversable */
	private $routes;

	/**
	 * @param array|\Traversable $routes
	 *
	 * @throws \IceHawk\IceHawk\Routing\Exceptions\RoutesAreNotTraversable
	 */
	public function __construct( $routes )
	{
		$this->guardRoutesAreTraversable( $routes );

		$this->routes = $routes;
	}

	/**
	 * @param mixed $routes
	 *
	 * @throws RoutesAreNotTraversable
	 */
	private function guardRoutesAreTraversable( $routes )
	{
		if ( !is_array( $routes ) && !($routes instanceof \Traversable) )
		{
			throw new RoutesAreNotTraversable( 'Routes are not traversable.' );
		}
	}

	/**
	 * @return array|\Traversable|RoutesToHandler[]
	 */
	final protected function getRoutes()
	{
		return $this->routes;
	}
}
