<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies\Interfaces;

use Closure;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
	public function register( string $id, Closure $createFunction ) : void;
}