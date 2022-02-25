<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use Stringable;

interface ConfigInterface
{
	/**
	 * @return iterable<string|Stringable>
	 */
	public function getAppMiddlewares() : iterable;

	public function getRoutes() : RoutesInterface;
}