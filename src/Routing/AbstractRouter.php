<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Interfaces\RoutesToHandler;
use Fortuneglobe\IceHawk\Routing\Exceptions\RoutesAreNotTraversable;

/**
 * Class AbstractRouter
 * @package Fortuneglobe\IceHawk\Routing
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