<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface RequestExceptionInterface extends IceHawkExceptionInterface
{
	public static function new(
		ServerRequestInterface $request,
		string $message,
		int $code = 0,
	) : RequestExceptionInterface;

	public static function fromPrevious(
		Throwable $previous,
		ServerRequestInterface $request
	) : RequestExceptionInterface;

	public function getRequest() : ServerRequestInterface;
}