<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types\Stubs;

use IceHawk\IceHawk\Messages\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class RequestHandlerImplementation implements RequestHandlerInterface
{
	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		return Response::new();
	}
}