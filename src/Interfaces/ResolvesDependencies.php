<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use Psr\Http\Server\MiddlewareInterface;

interface ResolvesDependencies
{
	public function getAppMiddlewares() : MiddlewareClassNames;

	public function getRoutes() : Routes;

	public function resolveMiddleware( MiddlewareClassName $middlewareClassName ) : MiddlewareInterface;
}