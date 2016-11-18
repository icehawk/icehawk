<?php
namespace IceHawk\IceHawk\Routing\Interfaces;

/**
 * Interface ProxiesRoutes
 *
 * @package IceHawk\IceHawk\Routing\Interfaces
 */
interface RedirectsRoute
{
	public function matches( string $uri ) : bool;

	public function getFinalUri() : string;

	public function getFinalMethod() : string;

	public function getUriParams() : array;
}