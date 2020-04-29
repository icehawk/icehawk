<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Stubs;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PassThroughMiddleware implements MiddlewareInterface
{
	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ) : ResponseInterface
	{
		return $handler->handle( $request->withAddedHeader( 'X-ID', self::class ) );
	}
}