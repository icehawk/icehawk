<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use Countable;
use IceHawk\IceHawk\Exceptions\RouteNotFoundException;
use IceHawk\IceHawk\Routing\Interfaces\ResolvesRouteToMiddlewares;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_unique;
use function count;

/**
 * @implements IteratorAggregate<int, Route>
 */
final class Routes implements Countable, IteratorAggregate
{
	/** @var array<int, Route> */
	private array $items;

	private function __construct( ResolvesRouteToMiddlewares ...$routes )
	{
		$this->items = $routes;
	}

	public static function new( ResolvesRouteToMiddlewares ...$routes ) : self
	{
		return new self( ...$routes );
	}

	public function add( ResolvesRouteToMiddlewares $route, ResolvesRouteToMiddlewares ...$routes ) : void
	{
		$this->items[] = $route;
		foreach ( $routes as $routeLoop )
		{
			$this->items[] = $routeLoop;
		}
	}

	/**
	 * @return Iterator<int, Route>
	 */
	public function getIterator() : Iterator
	{
		yield from $this->items;
	}

	public function count() : int
	{
		return count( $this->items );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 * @throws RouteNotFoundException
	 */
	public function findMatchingRouteForRequest( ServerRequestInterface $request ) : ResolvesRouteToMiddlewares
	{
		foreach ( $this->items as $route )
		{
			if ( $route->matchesRequest( $request ) )
			{
				return $route;
			}
		}

		throw RouteNotFoundException::newFromRequest( $request );
	}

	/**
	 * @param UriInterface $uri
	 *
	 * @return array<int, HttpMethod>
	 */
	public function findAcceptedHttpMethodsForUri( UriInterface $uri ) : array
	{
		$acceptedMethods = [];

		foreach ( $this->items as $route )
		{
			if ( !$route->matchesUri( $uri ) )
			{
				continue;
			}

			foreach ( $route->getAcceptedHttpMethods() as $acceptedHttpMethod )
			{
				$acceptedMethods[] = $acceptedHttpMethod;
			}
		}

		return array_unique( $acceptedMethods );
	}
}