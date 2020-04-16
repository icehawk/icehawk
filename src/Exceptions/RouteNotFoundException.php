<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function sprintf;

final class RouteNotFoundException extends RuntimeException
{
	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return RouteNotFoundException
	 */
	public static function newFromRequest( ServerRequestInterface $request ) : self
	{
		$message = sprintf(
			'Could not find route for requested method (%s) and URI: %s',
			$request->getMethod(),
			$request->getUri()
		);

		return new self( $message );
	}
}