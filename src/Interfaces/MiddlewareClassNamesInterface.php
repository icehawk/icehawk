<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int, string>
 */
interface MiddlewareClassNamesInterface extends IteratorAggregate, Countable
{
	public function add( string $className, string ...$classNames ) : void;

	public function append( MiddlewareClassNamesInterface $middlewareClassNames ) : MiddlewareClassNamesInterface;
}