<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\Interfaces\ConfigInterface;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Middlewares\OptionsMiddleware;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
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
	#[Pure]
	public static function new( ConfigInterface $config, ContainerInterface $diContainer ) : self
	{
		return new self( $config, $diContainer );
	}

	private function __construct(
		private readonly ConfigInterface $config,
		private readonly ContainerInterface $diContainer
	)
	{
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
		$routes       = $this->config->getRoutes();
		$routeHandler = QueueRequestHandler::new(
			FallbackRequestHandler::new( new LogicException( 'No responder found.', 404 ) )
		);

		$appHandler = QueueRequestHandler::new( $routeHandler );
		$appHandler->add( static fn() => OptionsMiddleware::new( $routes ) );

		foreach ( $this->config->getAppMiddlewares() as $middlewareClassName )
		{
			$appHandler->add( fn() => $this->diContainer->get( (string)$middlewareClassName ) );
		}

		$route = $routes->findMatchingRouteForRequest( $request );

		foreach ( $route->getMiddlewareClassNames() as $middlewareClassName )
		{
			$routeHandler->add( fn() => $this->diContainer->get( (string)$middlewareClassName ) );
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
			header( "{$headerName}: {$response->getHeaderLine($headerName)}", true );
		}

		$bodyStream = $response->getBody();

		echo $bodyStream;
		flush();

		$bodyStream->close();
	}
}