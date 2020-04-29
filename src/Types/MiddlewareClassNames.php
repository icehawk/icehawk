<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use Countable;
use Iterator;
use IteratorAggregate;
use function array_map;
use function count;

/**
 * @implements IteratorAggregate<int, MiddlewareClassName>
 */
final class MiddlewareClassNames implements Countable, IteratorAggregate
{
	/** @var array<int, MiddlewareClassName> */
	private array $items;

	private function __construct( MiddlewareClassName ...$middlewareClassNames )
	{
		$this->items = $middlewareClassNames;
	}

	public static function new( MiddlewareClassName ...$middlewareClassNames ) : self
	{
		return new self( ...$middlewareClassNames );
	}

	public static function newFromStrings( string ...$middlewareClassNames ) : self
	{
		return new self(
			...array_map(
				   fn( string $className ) : MiddlewareClassName => MiddlewareClassName::newFromString( $className ),
				   $middlewareClassNames
			   )
		);
	}

	public function add( MiddlewareClassName $middlewareClassName, MiddlewareClassName ...$middlewareClassNames ) : void
	{
		$this->items[] = $middlewareClassName;
		foreach ( $middlewareClassNames as $middlewareClassNameLoop )
		{
			$this->items[] = $middlewareClassNameLoop;
		}
	}

	/**
	 * @return Iterator<int, MiddlewareClassName>
	 */
	public function getIterator() : Iterator
	{
		yield from $this->items;
	}

	public function count() : int
	{
		return count( $this->items );
	}
}