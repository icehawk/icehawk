<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\Interfaces\ConfigInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Middlewares\OptionsMiddleware;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Routing\Interfaces\RouteInterface;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function array_keys;
use function flush;
use function header;
use function http_response_code;
use function session_status;
use function session_write_close;
use const PHP_SESSION_ACTIVE;

final class IceHawk
{
	public static function new( ContainerInterface $diContainer ) : self
	{
		return new self( $diContainer, MiddlewareClassNames::new(), Routes::new() );
	}

	public static function newFromConfig( ConfigInterface $config ) : self
	{
		return new self(
			$config->getDiContainer(),
			$config->getAppMiddlewares(),
			$config->getRoutes()
		);
	}

	private function __construct(
		private readonly ContainerInterface $diContainer,
		private readonly MiddlewareClassNamesInterface $appMiddlewares,
		private readonly RoutesInterface $routes
	)
	{
	}

	public function withAppMiddlewares( string $appMiddleware, string ...$appMiddlewares ) : self
	{
		return new self(
			$this->diContainer,
			MiddlewareClassNames::new( $appMiddleware, ...$appMiddlewares ),
			$this->routes
		);
	}

	public function withRoutes( RouteInterface $route, RouteInterface ...$routes ) : self
	{
		return new self(
			$this->diContainer,
			$this->appMiddlewares,
			Routes::new( $route, ...$routes )
		);
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @throws RequestHandlingFailedException
	 */
	public function handleRequest( ServerRequestInterface $request ) : void
	{
		$response = $this->getResponseForRequest( $request );

		$this->closeSessionIfActive();

		$response = $this->modifyResponseForHeadRequest( $request, $response );
		$response = $this->modifyResponseForTraceRequest( $request, $response );

		$this->respond( $response );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 * @throws RequestHandlingFailedException
	 */
	private function getResponseForRequest( ServerRequestInterface $request ) : ResponseInterface
	{
		$routeHandler = QueueRequestHandler::new(
			FallbackRequestHandler::new( new LogicException( 'No responder found.', 404 ) )
		);

		$appHandler = QueueRequestHandler::new( $routeHandler );
		$appHandler->add( fn() => OptionsMiddleware::new( $this->routes ) );

		foreach ( $this->appMiddlewares as $middlewareClassName )
		{
			$appHandler->add( fn() => $this->diContainer->get( $middlewareClassName ) );
		}

		$route = $this->routes->findMatchingRouteForRequest( $request );

		foreach ( $route->getMiddlewareClassNames() as $middlewareClassName )
		{
			$routeHandler->add( fn() => $this->diContainer->get( $middlewareClassName ) );
		}

		$request = $route->getModifiedRequest() ?? $request;

		return $appHandler->handle( $request );
	}

	private function closeSessionIfActive() : void
	{
		if ( PHP_SESSION_ACTIVE === session_status() )
		{
			session_write_close();
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 *
	 * @return ResponseInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	private function modifyResponseForHeadRequest(
		ServerRequestInterface $request,
		ResponseInterface $response
	) : ResponseInterface
	{
		if ( HttpMethod::HEAD->equalsString( $request->getMethod() ) )
		{
			return $response->withHeader( 'Content-Length', (string)$response->getBody()->getSize() )
			                ->withBody( Stream::newWithContent( '' ) );
		}

		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	private function modifyResponseForTraceRequest(
		ServerRequestInterface $request,
		ResponseInterface $response
	) : ResponseInterface
	{
		if ( HttpMethod::TRACE->equalsString( $request->getMethod() ) )
		{
			return $response->withBody( $request->getBody() )
			                ->withHeader( 'Content-Type', 'message/http' )
			                ->withStatus( 200 );
		}

		return $response;
	}

	private function respond( ResponseInterface $response ) : void
	{
		http_response_code( $response->getStatusCode() );

		foreach ( array_keys( $response->getHeaders() ) as $headerName )
		{
			header( "$headerName: {$response->getHeaderLine($headerName)}", true );
		}

		$bodyStream = $response->getBody();

		echo $bodyStream;
		flush();

		$bodyStream->close();
	}
}