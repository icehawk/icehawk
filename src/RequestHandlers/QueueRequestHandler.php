<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_shift;
use function get_class;

final class QueueRequestHandler implements RequestHandlerInterface
{
	/** @var array<int, callable> */
	private array $middlewares;

	private function __construct( private readonly RequestHandlerInterface $fallbackHandler )
	{
		$this->middlewares = [];
	}

	#[Pure]
	public static function new( RequestHandlerInterface $fallbackHandler ) : self
	{
		return new self( $fallbackHandler );
	}

	public function add( callable ...$middlewares ) : void
	{
		array_push( $this->middlewares, ...array_values( $middlewares ) );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 * @throws RequestHandlingFailedException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		if ( [] === $this->middlewares )
		{
			return $this->fallbackHandler->handle( $request );
		}

		/** @var callable $middlewareConstructor */
		$middlewareConstructor = array_shift( $this->middlewares );
		$middleware            = $middlewareConstructor();

		if ( !($middleware instanceof MiddlewareInterface) )
		{
			throw RequestHandlingFailedException::new(
				$request,
				'Instance did not resolve to a class implementing ' . MiddlewareInterface::class
			);
		}

		$response = $middleware->process( $request, $this );

		if ( HttpMethod::TRACE->equalsString( $request->getMethod() ) )
		{
			return $response->withAddedHeader( 'X-Trace', $middleware::class );
		}

		return $response;
	}
}