<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use Psr\Container\ContainerInterface;

interface ConfigInterface
{
	public function getDiContainer() : ContainerInterface;

	public function getAppMiddlewares() : MiddlewareClassNamesInterface;

	public function getRoutes() : RoutesInterface;
}