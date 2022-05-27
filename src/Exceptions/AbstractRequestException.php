<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Exceptions;

use Exception;
use IceHawk\IceHawk\Interfaces\RequestExceptionInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class AbstractRequestException extends Exception implements RequestExceptionInterface
{
	#[Pure]
	public static function new(
		ServerRequestInterface $request,
		string $message,
		int $code = 0,
	) : RequestExceptionInterface
	{
		return new static( $message, $code, null, $request );
	}

	public static function fromPrevious( Throwable $previous, ServerRequestInterface $request ) : static
	{
		return new static( $previous->getMessage(), $previous->getCode(), $previous, $request );
	}

	#[Pure]
	final private function __construct(
		string $message,
		int $code,
		?Throwable $previous,
		private readonly ServerRequestInterface $request
	)
	{
		parent::__construct( $message, $code, $previous );
	}

	public function getRequest() : ServerRequestInterface
	{
		return $this->request;
	}
}