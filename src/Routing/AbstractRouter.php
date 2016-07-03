<?php
/**
 * @author hollodotme
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
			throw new RoutesAreNotTraversable();
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