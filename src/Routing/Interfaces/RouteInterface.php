<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing\Interfaces;

use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

interface RouteInterface
{
	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function matchesRequest( ServerRequestInterface $request ) : bool;

	public function matchesUri( UriInterface $uri ) : bool;

	public function getMiddlewareClassNames() : MiddlewareClassNamesInterface;

	public function getModifiedRequest() : ?ServerRequestInterface;

	public function getAcceptedHttpMethods() : HttpMethodsInterface;

	public function matchAgainstFullUri() : RouteInterface;
}