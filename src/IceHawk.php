<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Exceptions\RouteNotFoundException;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\Middlewares\OptionsMiddleware;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function array_keys;
use function flush;
use function header;
use function http_response_code;
use function session_status;
use function session_write_close;
use const PHP_SESSION_ACTIVE;

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
	 * @throws InvalidArgumentException
	 */
	public function handleRequest( ServerRequestInterface $request ) : void
	{
		$response = $this->getResponseForRequest( $request );

		$this->closeSessionIfActive();
		$this->respond( $request, $response );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	private function getResponseForRequest( ServerRequestInterface $request ) : ResponseInterface
	{
		$routes       = $this->dependencies->getRoutes();
		$routeHandler = QueueRequestHandler::newWithFallbackHandler(
			FallbackRequestHandler::newWithMessage( 'Not Found.' )
		);

		$appHandler = QueueRequestHandler::newWithFallbackHandler( $routeHandler );
		$appHandler->add( OptionsMiddleware::newWithRoutes( $routes ) );

		foreach ( $this->dependencies->getAppMiddlewares() as $middlewareClassName )
		{
			$appHandler->add( $this->dependencies->resolveMiddleware( $middlewareClassName ) );
		}

		try
		{
			$route = $routes->findMatchingRouteForRequest( $request );

			foreach ( $route->getMiddlewareClassNames() as $middlewareClassName )
			{
				$routeHandler->add( $this->dependencies->resolveMiddleware( $middlewareClassName ) );
			}

			$request = $route->getModifiedRequest() ?? $request;
		}
		catch ( RouteNotFoundException $e )
		{
		}

		return $appHandler->handle( $request );
	}

	private function closeSessionIfActive() : void
	{
		if ( PHP_SESSION_ACTIVE === session_status() )
		{
			session_write_close();
		}
	}

	private function respond( ServerRequestInterface $request, ResponseInterface $response ) : void
	{
		http_response_code( $response->getStatusCode() );

		foreach ( array_keys( $response->getHeaders() ) as $headerName )
		{
			header( "{$headerName}: {$response->getHeaderLine($headerName)}", true );
		}

		if ( HttpMethod::head()->equalsString( $request->getMethod() ) )
		{
			header( 'Content-Length: ' . $response->getBody()->getSize(), true );

			return;
		}

		echo $response->getBody();
		flush();
	}
}