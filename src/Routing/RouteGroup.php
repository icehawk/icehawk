<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use IceHawk\IceHawk\Routing\Interfaces\RouteInterface;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_merge;

final class RouteGroup implements RouteInterface
{
	private ?ServerRequestInterface $modifiedRequest;

	private ?RouteInterface $foundRoute;

	private function __construct(
		private readonly RoutePattern $groupPattern,
		private readonly MiddlewareClassNamesInterface $groupMiddlewares,
		private readonly RoutesInterface $routes
	)
	{
		$this->modifiedRequest = null;
		$this->foundRoute      = null;
	}

	/**
	 * @param string         $groupPattern
	 * @param RouteInterface $route
	 * @param RouteInterface ...$routes
	 *
	 * @return RouteGroup
	 * @throws InvalidArgumentException
	 */
	public static function new( string $groupPattern, RouteInterface $route, RouteInterface ...$routes ) : self
	{
		return new self(
			RoutePattern::newFromString( $groupPattern ),
			MiddlewareClassNames::new(),
			Routes::new( $route, ...$routes )
		);
	}

	/**
	 * @param string         $groupPattern
	 * @param array<string>  $groupMiddlewares
	 * @param RouteInterface $route
	 * @param RouteInterface ...$routes
	 *
	 * @return RouteGroup
	 * @throws InvalidArgumentException
	 */
	public static function newWithGroupMiddlewares(
		string $groupPattern,
		array $groupMiddlewares,
		RouteInterface $route,
		RouteInterface ...$routes
	) : self
	{
		return new self(
			RoutePattern::newFromString( $groupPattern ),
			MiddlewareClassNames::new( ...$groupMiddlewares ),
			Routes::new( $route, ...$routes )
		);
	}

	public function matchesRequest( ServerRequestInterface $request ) : bool
	{
		if ( !$this->matchesUri( $request->getUri() ) )
		{
			return false;
		}

		$this->foundRoute = $this->routes->findMatchingRouteForRequest( $request );

		if ( $this->foundRoute instanceof NullRoute )
		{
			return false;
		}

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

	public function matchesUri( UriInterface $uri ) : bool
	{
		if ( !$this->groupPattern->matchesUri( $uri ) )
		{
			return false;
		}

		/** @var RouteInterface $route */
		foreach ( $this->routes as $route )
		{
			if ( $route->matchesUri( $uri ) )
			{
				return true;
			}
		}

		return false;
	}

	public function getMiddlewareClassNames() : MiddlewareClassNamesInterface
	{
		if ( null === $this->foundRoute )
		{
			return MiddlewareClassNames::new();
		}

		return $this->groupMiddlewares->append( $this->foundRoute->getMiddlewareClassNames() );
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest;
	}

	public function getAcceptedHttpMethods() : HttpMethodsInterface
	{
		return HttpMethods::all();
	}

	public function matchAgainstFullUri() : RouteInterface
	{
		$this->groupPattern->matchAgainstFullUri();

		/** @var RouteInterface $route */
		foreach ( $this->routes as $route )
		{
			$route->matchAgainstFullUri();
		}

		return $this;
	}
}