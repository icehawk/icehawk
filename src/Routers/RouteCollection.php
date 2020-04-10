<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routers;

use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function count;

/**
 * @implements IteratorAggregate<int, Route>
 */
final class RouteCollection implements Countable, IteratorAggregate
{
	/** @var array<int, Route> */
	private array $items;

	private function __construct( Route ...$routes )
	{
		$this->items = $routes;
	}

	public static function new( Route ...$routes ) : self
	{
		return new self( ...$routes );
	}

	public function add( Route $route, Route ...$routes ) : void
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
	 * @return Route
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function findMatchingRouteForRequest( ServerRequestInterface $request ) : Route
	{
		foreach ( $this->items as $route )
		{
			if ( $route->matchesRequest( $request ) )
			{
				return $route;
			}
		}

		throw new RuntimeException( 'Could not find route for request: ' . $request->getUri() );
	}
}