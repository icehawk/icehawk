<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;
use function array_keys;
use function http_response_code;

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
		try
		{
			$route = $this->dependencies->getRoutes()->findMatchingRouteForRequest( $request );

			$requestHandler = $this->dependencies->resolveRequestHandler(
				$route->getRequestHandlerClassName(),
				...$route->getMiddlewareClassNames()
			);

			$response = $requestHandler->handle( $route->getModifiedRequest() ?? $request );
		}
		catch ( Throwable $e )
		{
			$message  = sprintf( 'Exception occurred: %s', $e->getMessage() );
			$response = FallbackRequestHandler::newWithMessage( $message )->handle( $request );
		}

		http_response_code( $response->getStatusCode() );
		
		foreach ( array_keys( $response->getHeaders() ) as $headerName )
		{
			header( "{$headerName}: {$response->getHeaderLine($headerName)}", true );
		}

		echo $response->getBody();
		flush();
	}
}