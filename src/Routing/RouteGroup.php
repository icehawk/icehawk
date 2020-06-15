<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Exceptions\RouteNotFoundException;
use IceHawk\IceHawk\Routing\Interfaces\ResolvesRouteToMiddlewares;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_merge;

final class RouteGroup implements ResolvesRouteToMiddlewares
{
	private RoutePattern $groupPattern;

	private Routes $routes;

	private ?ServerRequestInterface $modifiedRequest;

	private ?ResolvesRouteToMiddlewares $foundRoute;

	private function __construct( RoutePattern $groupPattern, Routes $routes )
	{
		$this->groupPattern    = $groupPattern;
		$this->routes          = $routes;
		$this->modifiedRequest = null;
		$this->foundRoute      = null;
	}

	/**
	 * @param string                     $groupPattern
	 * @param ResolvesRouteToMiddlewares $route
	 * @param ResolvesRouteToMiddlewares ...$routes
	 *
	 * @return static
	 * @throws InvalidArgumentException
	 */
	public static function new(
		string $groupPattern,
		ResolvesRouteToMiddlewares $route,
		ResolvesRouteToMiddlewares ...$routes
	) : self
	{
		return new self(
			RoutePattern::newFromString( $groupPattern ),
			Routes::new( $route, ...$routes )
		);
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function matchesRequest( ServerRequestInterface $request ) : bool
	{
		if ( !$this->matchesUri( $request->getUri() ) )
		{
			return false;
		}

		try
		{
			$this->foundRoute         = $this->routes->findMatchingRouteForRequest( $request );
			$modifiedRequestFromRoute = $this->foundRoute->getModifiedRequest();

			if ( $modifiedRequestFromRoute instanceof ServerRequestInterface )
			{
				$this->modifiedRequest = $modifiedRequestFromRoute->withQueryParams(
					array_merge(
						$modifiedRequestFromRoute->getQueryParams(),
						$this->groupPattern->getMatches()
					)
				);
			}

			return true;
		}
		catch ( RouteNotFoundException $e )
		{
			return false;
		}
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		if ( !$this->groupPattern->matchesUri( $uri ) )
		{
			return false;
		}

		/** @var ResolvesRouteToMiddlewares $route */
		foreach ( $this->routes as $route )
		{
			if ( $route->matchesUri( $uri ) )
			{
				return true;
			}
		}

		return false;
	}

	public function getMiddlewareClassNames() : MiddlewareClassNames
	{
		return $this->foundRoute->getMiddlewareClassNames();
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest;
	}

	public function getAcceptedHttpMethods() : HttpMethods
	{
		return HttpMethods::all();
	}

	public function matchAgainstFullUri() : ResolvesRouteToMiddlewares
	{
		$this->groupPattern->matchAgainstFullUri();

		/** @var ResolvesRouteToMiddlewares $route */
		foreach ( $this->routes as $route )
		{
			$route->matchAgainstFullUri();
		}

		return $this;
	}
}