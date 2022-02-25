<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing\Interfaces;

use Countable;
use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IteratorAggregate;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @extends IteratorAggregate<int, RouteInterface>
 */
interface RoutesInterface extends IteratorAggregate, Countable
{
	public function add( RouteInterface $route, RouteInterface ...$routes ) : void;

	public function findMatchingRouteForRequest( ServerRequestInterface $request ) : RouteInterface;

	public function findAcceptedHttpMethodsForUri( UriInterface $uri ) : HttpMethodsInterface;
}