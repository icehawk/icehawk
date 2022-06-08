<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use Iterator;
use function array_push;
use function array_values;
use function count;

final class MiddlewareClassNames implements MiddlewareClassNamesInterface
{
	public static function new( string ...$classNames ) : self
	{
		return new self( array_values( $classNames ) );
	}

	/**
	 * @param array<int, string> $classNames
	 */
	private function __construct( private array $classNames ) { }

	public function add( string $className, string ...$classNames ) : void
	{
		array_push( $this->classNames, $className, ...array_values( $classNames ) );
	}

	public function append( MiddlewareClassNamesInterface $middlewareClassNames ) : MiddlewareClassNamesInterface
	{
		return new self( [...$this->classNames, ...$middlewareClassNames] );
	}

	/**
	 * @return Iterator<int, string>
	 */
	public function getIterator() : Iterator
	{
		yield from $this->classNames;
	}

	public function count() : int
	{
		return count( $this->classNames );
	}
}