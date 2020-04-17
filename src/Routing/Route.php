<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use IceHawk\IceHawk\Types\RoutePattern;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_map;

final class Route
{
	private const DEFAULT_REQUEST_HANDLER_CLASS_NAME = QueueRequestHandler::class;

	private HttpMethod $httpMethod;

	/** @var array<int, HttpMethod> */
	private array $acceptedHttpMethods;

	private RequestHandlerClassName $requestHandlerClassName;

	/** @var array<int, MiddlewareClassName> */
	private array $middlewareClassNames;

	private RoutePattern $routePattern;

	private ?ServerRequestInterface $modifiedRequest;

	/**
	 * @param HttpMethod                      $httpMethod
	 * @param RoutePattern                    $routePattern
	 * @param RequestHandlerClassName         $requestHandlerClassName
	 * @param array<int, MiddlewareClassName> $middlewareClassNames
	 */
	private function __construct(
		HttpMethod $httpMethod,
		RoutePattern $routePattern,
		RequestHandlerClassName $requestHandlerClassName,
		MiddlewareClassName  ...$middlewareClassNames
	)
	{
		$this->httpMethod              = $httpMethod;
		$this->requestHandlerClassName = $requestHandlerClassName;
		$this->middlewareClassNames    = $middlewareClassNames;
		$this->routePattern            = $routePattern;

		$this->setAcceptedHttpMethods();
	}

	private function setAcceptedHttpMethods() : void
	{
		$this->acceptedHttpMethods = [
			$this->httpMethod,
			HttpMethod::connect(),
			HttpMethod::options(),
			HttpMethod::trace(),
		];

		if ( $this->httpMethod->equalsOneOf( HttpMethod::get() ) )
		{
			$this->acceptedHttpMethods[] = HttpMethod::head();
		}
	}

	/**
	 * @param string             $httpMethod
	 * @param string             $regexPattern
	 * @param string             $requestHandlerClassName
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function newFromStrings(
		string $httpMethod,
		string $regexPattern,
		string $requestHandlerClassName,
		string ...$middlewareClassNames
	) : self
	{
		return new self(
			HttpMethod::newFromString( $httpMethod ),
			RoutePattern::newFromString( $regexPattern ),
			RequestHandlerClassName::newFromString( $requestHandlerClassName ),
			...
			array_map(
				fn( string $item ) : MiddlewareClassName => MiddlewareClassName::newFromString( $item ),
				$middlewareClassNames
			)
		);
	}

	/**
	 * @param string             $regexPattern
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function get( string $regexPattern, string ...$middlewareClassNames ) : self
	{
		return self::newFromStrings(
			'GET',
			$regexPattern,
			self::DEFAULT_REQUEST_HANDLER_CLASS_NAME,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string             $regexPattern
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function post( string $regexPattern, string ...$middlewareClassNames ) : self
	{
		return self::newFromStrings(
			'POST',
			$regexPattern,
			self::DEFAULT_REQUEST_HANDLER_CLASS_NAME,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string             $regexPattern
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function put( string $regexPattern, string ...$middlewareClassNames ) : self
	{
		return self::newFromStrings(
			'PUT',
			$regexPattern,
			self::DEFAULT_REQUEST_HANDLER_CLASS_NAME,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string             $regexPattern
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function patch( string $regexPattern, string ...$middlewareClassNames ) : self
	{
		return self::newFromStrings(
			'PATCH',
			$regexPattern,
			self::DEFAULT_REQUEST_HANDLER_CLASS_NAME,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string             $regexPattern
	 * @param array<int, string> $middlewareClassNames
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public static function delete( string $regexPattern, string ...$middlewareClassNames ) : self
	{
		return self::newFromStrings(
			'DELETE',
			$regexPattern,
			self::DEFAULT_REQUEST_HANDLER_CLASS_NAME,
			...$middlewareClassNames
		);
	}

	/**
	 * @param string $requestHandlerClassName
	 *
	 * @return Route
	 * @throws InvalidArgumentException
	 */
	public function withRequestHandlerClassName( string $requestHandlerClassName ) : self
	{
		$route                          = clone $this;
		$route->requestHandlerClassName = RequestHandlerClassName::newFromString( $requestHandlerClassName );

		return $route;
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

	/**
	 * @return array<int, HttpMethod>
	 */
	public function getAcceptedHttpMethods() : array
	{
		return $this->acceptedHttpMethods;
	}
}