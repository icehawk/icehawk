<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use Exception;
use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IceHawk\IceHawk\Routing\Interfaces\RouteInterface;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Types\HttpMethods;
use InvalidArgumentException;
use Iterator;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_push;
use function array_values;
use function count;

final class Routes implements RoutesInterface
{
	/**
	 * @param array<int, RouteInterface> $routes
	 */
	private function __construct( private array $routes ) { }

	#[Pure]
	public static function new( RouteInterface ...$routes ) : self
	{
		return new self( array_values( $routes ) );
	}

	public function add( RouteInterface $route, RouteInterface ...$routes ) : void
	{
		array_push( $this->routes, $route, ...array_values( $routes ) );
	}

	/**
	 * @return Iterator<int, RouteInterface>
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
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public function findMatchingRouteForRequest( ServerRequestInterface $request ) : RouteInterface
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
	 * @return HttpMethodsInterface
	 * @throws Exception
	 */
	public function findAcceptedHttpMethodsForUri( UriInterface $uri ) : HttpMethodsInterface
	{
		$acceptedMethods = HttpMethods::new();

		foreach ( $this->routes as $route )
		{
			if ( !$route->matchesUri( $uri ) )
			{
				continue;
			}

			$acceptedMethods->add( ...$route->getAcceptedHttpMethods()->getIterator() );
		}

		return $acceptedMethods;
	}
}