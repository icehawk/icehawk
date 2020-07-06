<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

final class RequestHandlingFailedException extends RuntimeException
{
	private ServerRequestInterface $request;

	public static function newFromPrevious( Throwable $previous, ServerRequestInterface $request ) : self
	{
		$exception          = new self( $previous->getMessage(), $previous->getCode(), $previous );
		$exception->request = $request;

		return $exception;
	}

	public function getRequest() : ServerRequestInterface
	{
		return $this->request;
	}
}