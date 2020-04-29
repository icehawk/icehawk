<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_merge;
use function array_shift;
use function get_class;

final class QueueRequestHandler implements RequestHandlerInterface
{
	private RequestHandlerInterface $fallbackHandler;

	/** @var array<int, MiddlewareInterface> */
	private array $middlewares;

	private function __construct( RequestHandlerInterface $fallbackHandler )
	{
		$this->fallbackHandler = $fallbackHandler;
		$this->middlewares     = [];
	}

	public static function newWithFallbackHandler( RequestHandlerInterface $fallbackHandler ) : self
	{
		return new self( $fallbackHandler );
	}

	public function add( MiddlewareInterface ...$middlewares ) : void
	{
		$this->middlewares = array_merge( $this->middlewares, $middlewares );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		if ( [] === $this->middlewares )
		{
			return $this->fallbackHandler->handle( $request );
		}

		/** @var MiddlewareInterface $middleware */
		$middleware = array_shift( $this->middlewares );

		$response = $middleware->process( $request, $this );

		if ( HttpMethod::trace()->equalsString( $request->getMethod() ) )
		{
			return $response->withAddedHeader( 'X-IceHawk-Trace', get_class( $middleware ) );
		}

		return $response;
	}
}