<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use Countable;
use Iterator;
use IteratorAggregate;
use function count;

/**
 * @implements IteratorAggregate<int, HttpMethod>
 */
final class HttpMethods implements Countable, IteratorAggregate
{
	/** @var array<int, HttpMethod> */
	private array $items;

	private function __construct( HttpMethod ...$httpMethods )
	{
		$this->items = $httpMethods;
	}

	public static function new( HttpMethod ...$httpMethods ) : self
	{
		return new self( ...$httpMethods );
	}

	public function add( HttpMethod $httpMethod, HttpMethod ...$httpMethods ) : void
	{
		$this->items[] = $httpMethod;
		foreach ( $httpMethods as $httpMethodLoop )
		{
			$this->items[] = $httpMethodLoop;
		}
	}

	/**
	 * @return Iterator<int, HttpMethod>
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