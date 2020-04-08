<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_merge;
use function array_shift;

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

	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		if ( [] === $this->middlewares )
		{
			return $this->fallbackHandler->handle( $request );
		}

		/** @var MiddlewareInterface $middleware */
		$middleware = array_shift( $this->middlewares );

		return $middleware->process( $request, $this );
	}
}