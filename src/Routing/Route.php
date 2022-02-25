<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use Exception;
use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use IceHawk\IceHawk\Routing\Interfaces\RouteInterface;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_merge;

final class Route implements RouteInterface
{
	private HttpMethod $httpMethod;

	private HttpMethodsInterface $acceptedHttpMethods;

	private MiddlewareClassNamesInterface $middlewareClassNames;

	private RoutePattern $routePattern;

	private ?ServerRequestInterface $modifiedRequest;

	private function __construct(
		HttpMethod $httpMethod,
		RoutePattern $routePattern,
		MiddlewareClassNamesInterface $middlewareClassNames
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
			HttpMethod::CONNECT,
			HttpMethod::OPTIONS,
			HttpMethod::TRACE
		);

		if ( $this->httpMethod->equalsOneOf( HttpMethod::GET ) )
		{
			$this->acceptedHttpMethods->add( HttpMethod::HEAD );
		}
	}

	/**
	 * @param HttpMethod $httpMethod
	 * @param string     $regexPattern
	 * @param string     ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function newFromStrings(
		HttpMethod $httpMethod,
		string $regexPattern,
		string ...$middlewareClassNames
	) : RouteInterface
	{
		return new self(
			$httpMethod,
			RoutePattern::newFromString( $regexPattern ),
			MiddlewareClassNames::newFromStrings( ...$middlewareClassNames )
		);
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function get( string $regexPattern, string ...$middlewareClassNames ) : RouteInterface
	{
		return self::newFromStrings( HttpMethod::GET, $regexPattern, ...$middlewareClassNames );
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function post( string $regexPattern, string ...$middlewareClassNames ) : RouteInterface
	{
		return self::newFromStrings( HttpMethod::POST, $regexPattern, ...$middlewareClassNames );
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function put( string $regexPattern, string ...$middlewareClassNames ) : RouteInterface
	{
		return self::newFromStrings( HttpMethod::PUT, $regexPattern, ...$middlewareClassNames );
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function patch( string $regexPattern, string ...$middlewareClassNames ) : RouteInterface
	{
		return self::newFromStrings( HttpMethod::PATCH, $regexPattern, ...$middlewareClassNames );
	}

	/**
	 * @param string $regexPattern
	 * @param string ...$middlewareClassNames
	 *
	 * @return RouteInterface
	 * @throws InvalidArgumentException
	 */
	public static function delete( string $regexPattern, string ...$middlewareClassNames ) : RouteInterface
	{
		return self::newFromStrings( HttpMethod::DELETE, $regexPattern, ...$middlewareClassNames );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function matchesRequest( ServerRequestInterface $request ) : bool
	{
		$httpMethod = HttpMethod::tryFrom( $request->getMethod() );

		if ( null === $httpMethod )
		{
			return false;
		}

		if ( !$this->acceptsHttpMethod( $httpMethod ) )
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

	/**
	 * @param HttpMethod $requestMethod
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function acceptsHttpMethod( HttpMethod $requestMethod ) : bool
	{
		return $requestMethod->equalsOneOf( ...$this->acceptedHttpMethods->getIterator() );
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		return $this->routePattern->matchesUri( $uri );
	}

	public function getMiddlewareClassNames() : MiddlewareClassNamesInterface
	{
		return $this->middlewareClassNames;
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest ?? null;
	}

	public function getAcceptedHttpMethods() : HttpMethodsInterface
	{
		return $this->acceptedHttpMethods;
	}

	public function matchAgainstFullUri() : RouteInterface
	{
		$this->routePattern->matchAgainstFullUri();

		return $this;
	}
}