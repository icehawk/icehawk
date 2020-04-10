<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use Countable;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
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
	private const DEFAULT_REQUEST_HANDLER_CLASS_NAME = QueueRequestHandler::class;

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

	/**
	 * @param array<string, array<string, mixed>> $configArray
	 *
	 * @return RouteCollection
	 * @throws InvalidArgumentException
	 */
	public static function fromConfigArray( array $configArray ) : self
	{
		$collection = new self();
		foreach ( $configArray as $routePattern => $routeInfo )
		{
			$collection->add(
				Route::newFromStrings(
					(string)($routeInfo['method'] ?? 'GET'),
					(string)$routePattern,
					(string)($routeInfo['handler'] ?? self::DEFAULT_REQUEST_HANDLER_CLASS_NAME),
					...(array)($routeInfo['middlewares'] ?? [])
				)
			);
		}

		return $collection;
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