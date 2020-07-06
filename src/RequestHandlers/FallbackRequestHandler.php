<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class FallbackRequestHandler implements RequestHandlerInterface
{
	private Throwable $exception;

	private function __construct( Throwable $exception )
	{
		$this->exception = $exception;
	}

	public static function newWithException( Throwable $exception ) : self
	{
		return new self( $exception );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws RequestHandlingFailedException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		throw RequestHandlingFailedException::newFromPrevious( $this->exception, $request );
	}
}