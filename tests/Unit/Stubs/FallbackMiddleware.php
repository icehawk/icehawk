<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Stubs;

use IceHawk\IceHawk\Messages\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class FallbackMiddleware implements MiddlewareInterface
{
	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ) : ResponseInterface
	{
		return Response::new()->withHeader( 'X-ID', self::class );
	}
}