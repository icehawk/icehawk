<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use Psr\Http\Server\RequestHandlerInterface;

interface ResolvesDependencies
{
	public function getRoutes() : RouteCollection;

	public function resolveRequestHandler(
		RequestHandlerClassName $handlerClassName,
		MiddlewareClassName ...$middlewareClassNames
	) : RequestHandlerInterface;
}