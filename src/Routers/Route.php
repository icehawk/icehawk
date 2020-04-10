<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routers;

use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use IceHawk\IceHawk\Types\RoutePattern;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use function array_map;

final class Route
{
	private HttpMethod $httpMethod;

	private RequestHandlerClassName $requestHandlerClassName;

	/** @var array<int, MiddlewareClassName> */
	private array $middlewareClassNames;

	private RoutePattern $routePattern;

	private ?ServerRequestInterface $modifiedRequest;

	/**
	 * @param HttpMethod                      $httpMethod
	 * @param RequestHandlerClassName         $requestHandlerClassName
	 * @param array<int, MiddlewareClassName> $middlewareClassNames
	 * @param RoutePattern                    $routePattern
	 */
	private function __construct(
		HttpMethod $httpMethod,
		RequestHandlerClassName $requestHandlerClassName,
		array $middlewareClassNames,
		RoutePattern $routePattern
	)
	{
		$this->httpMethod              = $httpMethod;
		$this->requestHandlerClassName = $requestHandlerClassName;
		$this->middlewareClassNames    = $middlewareClassNames;
		$this->routePattern            = $routePattern;
	}

	/**
	 * @param string                          $httpMethod
	 * @param string                          $requestHandlerClassName
	 * @param array<int, MiddlewareClassName> $middlewareClassNames
	 * @param string                          $regexPattern
	 * @param string                          $flags
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function newFromStrings(
		string $httpMethod,
		string $requestHandlerClassName,
		array $middlewareClassNames,
		string $regexPattern,
		string $flags = ''
	) : self
	{
		return new self(
			HttpMethod::newFromString( $httpMethod ),
			RequestHandlerClassName::newFromString( $requestHandlerClassName ),
			array_map( fn( string $item ) => MiddlewareClassName::newFromString( $item ), $middlewareClassNames ),
			RoutePattern::newFromString( $regexPattern, $flags )
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
		$requestMethod = HttpMethod::newFromString( $request->getMethod() );

		if ( !$this->httpMethod->equals( $requestMethod ) )
		{
			return false;
		}

		if ( !$this->routePattern->matchesUri( $request->getUri() ) )
		{
			return false;
		}

		$this->modifiedRequest = $request->withQueryParams(
			array_merge( $request->getQueryParams(), $this->routePattern->getMatches() )
		);

		return true;
	}

	public function getRequestHandlerClassName() : RequestHandlerClassName
	{
		return $this->requestHandlerClassName;
	}

	/**
	 * @return array<int, MiddlewareClassName>
	 */
	public function getMiddlewareClassNames() : array
	{
		return $this->middlewareClassNames;
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest ?? null;
	}
}