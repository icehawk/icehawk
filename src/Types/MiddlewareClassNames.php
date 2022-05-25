<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use InvalidArgumentException;
use Iterator;
use JetBrains\PhpStorm\Pure;
use function array_map;
use function array_push;
use function array_values;
use function count;

final class MiddlewareClassNames implements MiddlewareClassNamesInterface
{
	/**
	 * @param array<int, MiddlewareClassName> $classNames
	 */
	private function __construct( private array $classNames ) { }

	#[Pure]
	public static function new( MiddlewareClassName ...$classNames ) : self
	{
		return new self( array_values( $classNames ) );
	}

	/**
	 * @param string ...$classNames
	 *
	 * @return MiddlewareClassNames
	 * @throws InvalidArgumentException
	 */
	public static function newFromStrings( string ...$classNames ) : self
	{
		return self::new(
			...array_map(
				static fn( string $className ) : MiddlewareClassName => MiddlewareClassName::new( $className ),
				$classNames
			)
		);
	}

	public function add( MiddlewareClassName $className, MiddlewareClassName ...$classNames ) : void
	{
		array_push( $this->classNames, $className, ...array_values( $classNames ) );
	}

	public function append( MiddlewareClassNamesInterface $middlewareClassNames ) : MiddlewareClassNamesInterface
	{
		return new self( [...$this->classNames, ...$middlewareClassNames] );
	}

	/**
	 * @return Iterator<int, MiddlewareClassName>
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