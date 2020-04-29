<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Stubs;

use IceHawk\IceHawk\Messages\Interfaces\ProvidesRequestData;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Middlewares\AbstractMiddleware;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FallbackMiddleware extends AbstractMiddleware
{
	/**
	 * @param ProvidesRequestData     $request
	 * @param RequestHandlerInterface $next
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	protected function processRequest( ProvidesRequestData $request, RequestHandlerInterface $next ) : ResponseInterface
	{
		return Response::new()->withAddedHeader( 'X-ID', self::class );
	}
}