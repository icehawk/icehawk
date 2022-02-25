<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Middlewares;

use IceHawk\IceHawk\Messages\Interfaces\RequestInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	final public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	) : ResponseInterface
	{
		if ( !($request instanceof RequestInterface) )
		{
			throw new InvalidArgumentException(
				'IceHawk middleware expects an implementation of '
				. RequestInterface::class
				. ' as request object, '
				. get_class( $request )
				. ' given.'
			);
		}

		return $this->processRequest( $request, $handler );
	}

	abstract protected function processRequest(
		RequestInterface $request,
		RequestHandlerInterface $next
	) : ResponseInterface;
}