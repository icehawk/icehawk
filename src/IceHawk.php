<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Exceptions\RouteNotFoundException;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\OptionsRequestHandler;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function array_keys;
use function http_response_code;
use function sprintf;

final class IceHawk
{
	private ResolvesDependencies $dependencies;

	private function __construct( ResolvesDependencies $dependencies )
	{
		$this->dependencies = $dependencies;
	}

	public static function newWithDependencies( ResolvesDependencies $dependencies ) : self
	{
		return new self( $dependencies );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function handleRequest( ServerRequestInterface $request ) : void
	{
		$response = $this->getResponseForRequest( $request );

		http_response_code( $response->getStatusCode() );

		foreach ( array_keys( $response->getHeaders() ) as $headerName )
		{
			header( "{$headerName}: {$response->getHeaderLine($headerName)}", true );
		}

		if ( !HttpMethod::head()->equalsString( $request->getMethod() ) )
		{
			echo $response->getBody();
			flush();
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	private function getResponseForRequest( ServerRequestInterface $request ) : ResponseInterface
	{
		$routes = $this->dependencies->getRoutes();

		try
		{
			$route = $routes->findMatchingRouteForRequest( $request );

			$requestHandler = $this->dependencies->resolveRequestHandler(
				$route->getRequestHandlerClassName(),
				...$route->getMiddlewareClassNames()
			);

			$response = OptionsRequestHandler::new( $routes, $requestHandler )
			                                 ->handle( $route->getModifiedRequest() ?? $request );
		}
		catch ( RouteNotFoundException $e )
		{
			$message  = sprintf( 'Exception occurred: %s', $e->getMessage() );
			$response = FallbackRequestHandler::newWithMessage( $message )->handle( $request );
		}

		return $response;
	}
}