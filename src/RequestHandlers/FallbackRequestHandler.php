<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class FallbackRequestHandler implements RequestHandlerInterface
{
	#[Pure]
	public static function new( Throwable $exception ) : self
	{
		return new self( $exception );
	}

	private function __construct( private Throwable $exception ) { }

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws RequestHandlingFailedException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		throw RequestHandlingFailedException::fromPrevious( $this->exception, $request );
	}
}