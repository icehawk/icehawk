<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use Iterator;
use JetBrains\PhpStorm\Pure;
use function array_values;
use function count;

final class HttpMethods implements HttpMethodsInterface
{
	/**
	 * @param array<int, HttpMethod> $httpMethods
	 */
	private function __construct( private array $httpMethods ) { }

	#[Pure]
	public static function new( HttpMethod ...$httpMethods ) : HttpMethodsInterface
	{
		return new self( array_values( $httpMethods ) );
	}

	public static function all() : HttpMethodsInterface
	{
		return self::new( ...HttpMethod::cases() );
	}

	public function add( HttpMethod $httpMethod, HttpMethod ...$httpMethods ) : void
	{
		foreach ( [$httpMethod, ...$httpMethods] as $method )
		{
			if ( !in_array( $method, $this->httpMethods, true ) )
			{
				$this->httpMethods[] = $method;
			}
		}
	}

	/**
	 * @return Iterator<int, HttpMethod>
	 */
	public function getIterator() : Iterator
	{
		yield from $this->httpMethods;
	}

	public function count() : int
	{
		return count( $this->httpMethods );
	}

	/**
	 * @return array<string>
	 */
	public function asStringArray() : array
	{
		return array_map( static fn( HttpMethod $method ) : string => $method->toString(), $this->httpMethods );
	}
}