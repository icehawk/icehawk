<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use Countable;
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
 * @implements IteratorAggregate<int, ResolvesRouteToMiddlewares>
 */
final class Routes implements Countable, IteratorAggregate
{
	/** @var array<int, ResolvesRouteToMiddlewares> */
	private array $routes;

	private function __construct( ResolvesRouteToMiddlewares ...$routes )
	{
		$this->routes = $routes;
	}

	public static function new( ResolvesRouteToMiddlewares ...$routes ) : self
	{
		return new self( ...$routes );
	}

	public function add( ResolvesRouteToMiddlewares $route, ResolvesRouteToMiddlewares ...$routes ) : void
	{
		$this->routes[] = $route;
		foreach ( $routes as $routeLoop )
		{
			$this->routes[] = $routeLoop;
		}
	}

	/**
	 * @return Iterator<int, ResolvesRouteToMiddlewares>
	 */
	public function getIterator() : Iterator
	{
		yield from $this->routes;
	}

	public function count() : int
	{
		return count( $this->routes );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public function findMatchingRouteForRequest( ServerRequestInterface $request ) : ResolvesRouteToMiddlewares
	{
		foreach ( $this->routes as $route )
		{
			if ( $route->matchesRequest( $request ) )
			{
				return $route;
			}
		}

		return NullRoute::new( $request );
	}

	/**
	 * @param UriInterface $uri
	 *
	 * @return array<int, HttpMethod>
	 */
	public function findAcceptedHttpMethodsForUri( UriInterface $uri ) : array
	{
		$acceptedMethods = [];

		foreach ( $this->routes as $route )
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