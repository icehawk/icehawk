<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing\Interfaces;

use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

interface ResolvesRouteToMiddlewares
{
	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function matchesRequest( ServerRequestInterface $request ) : bool;

	public function matchesUri( UriInterface $uri ) : bool;

	public function getMiddlewareClassNames() : MiddlewareClassNames;

	public function getModifiedRequest() : ?ServerRequestInterface;

	public function getAcceptedHttpMethods() : HttpMethods;

	public function matchAgainstFullUri() : ResolvesRouteToMiddlewares;
}