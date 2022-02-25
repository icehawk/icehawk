<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use Countable;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int, MiddlewareClassName>
 */
interface MiddlewareClassNamesInterface extends IteratorAggregate, Countable
{
	public function add( MiddlewareClassName $className, MiddlewareClassName ...$classNames ) : void;
}