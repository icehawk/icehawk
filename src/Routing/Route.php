<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Routing\Interfaces\ResolvesRouteToMiddlewares;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_merge;

final class Route implements ResolvesRouteToMiddlewares
{
	private HttpMethod $httpMethod;

	private HttpMethods $acceptedHttpMethods;

	private MiddlewareClassNames $middlewareClassNames;

	private RoutePattern $routePattern;

	private ?ServerRequestInterface $modifiedRequest;

	/**
	 * @param HttpMethod           $httpMethod
	 * @param RoutePattern         $routePattern
	 * @param MiddlewareClassNames $middlewareClassNames
	 */
	private function __construct(
		HttpMethod $httpMethod,
		RoutePattern $routePattern,
		MiddlewareClassNames $middlewareClassNames
	)
	{
		$this->httpMethod           = $httpMethod;
		$this->middlewareClassNames = $middlewareClassNames;
		$this->routePattern         = $routePattern;

		$this->setAcceptedHttpMethods();
	}

	private function setAcceptedHttpMethods() : void
	{
		$this->acceptedHttpMethods = HttpMethods::new(
			$this->httpMethod,
			HttpMethod::connect(),
			HttpMethod::options(),
			HttpMethod::trace()
		);

		if ( $this->httpMethod->equalsOneOf( HttpMethod::get() ) )
		{
			$this->acceptedHttpMethods->add( HttpMethod::head() );
		}
	}

	/**
	 * @param string $httpMethod
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function newFromStrings(
		string $httpMethod,
		string $regexPattern,
		string ...$middlewareClassNames
	) : ResolvesRouteToMiddlewares
	{
		return new self(
			HttpMethod::newFromString( $httpMethod ),
			RoutePattern::newFromString( $regexPattern ),
			MiddlewareClassNames::newFromStrings( ...$middlewareClassNames )
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function get( string $regexPattern, string ...$middlewareClassNames ) : ResolvesRouteToMiddlewares
	{
		return self::newFromStrings(
			'GET',
			$regexPattern,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function post( string $regexPattern, string ...$middlewareClassNames ) : ResolvesRouteToMiddlewares
	{
		return self::newFromStrings(
			'POST',
			$regexPattern,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function put( string $regexPattern, string ...$middlewareClassNames ) : ResolvesRouteToMiddlewares
	{
		return self::newFromStrings(
			'PUT',
			$regexPattern,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function patch( string $regexPattern, string ...$middlewareClassNames ) : ResolvesRouteToMiddlewares
	{
		return self::newFromStrings(
			'PATCH',
			$regexPattern,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return ResolvesRouteToMiddlewares
	 * @throws InvalidArgumentException
	 */
	public static function delete( string $regexPattern, string ...$middlewareClassNames ) : ResolvesRouteToMiddlewares
	{
		return self::newFromStrings(
			'DELETE',
			$regexPattern,
			...$middlewareClassNames
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
		if ( !$this->acceptsHttpMethod( HttpMethod::newFromString( $request->getMethod() ) ) )
		{
			return false;
		}

		if ( !$this->matchesUri( $request->getUri() ) )
		{
			return false;
		}

		$this->modifiedRequest = $request->withQueryParams(
			array_merge( $request->getQueryParams(), $this->routePattern->getMatches() )
		);

		return true;
	}

	private function acceptsHttpMethod( HttpMethod $requestMethod ) : bool
	{
		return $requestMethod->equalsOneOf( ...$this->acceptedHttpMethods );
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		return $this->routePattern->matchesUri( $uri );
	}

	public function getMiddlewareClassNames() : MiddlewareClassNames
	{
		return $this->middlewareClassNames;
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest ?? null;
	}

	public function getAcceptedHttpMethods() : HttpMethods
	{
		return $this->acceptedHttpMethods;
	}

	public function matchAgainstFullUri() : ResolvesRouteToMiddlewares
	{
		$this->routePattern->matchAgainstFullUri();

		return $this;
	}
}