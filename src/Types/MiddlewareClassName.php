<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Stringable;
use function class_exists;

final class MiddlewareClassName implements Stringable
{
	/**
	 * @param string $middlewareClassName
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( private string $middlewareClassName )
	{
		$this->guardMiddlewareClassNameIsValid( $middlewareClassName );
	}

	/**
	 * @param string $middlewareClassName
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardMiddlewareClassNameIsValid( string $middlewareClassName ) : void
	{
		if ( !class_exists( $middlewareClassName ) )
		{
			throw new InvalidArgumentException( 'Middleware class does not exist: ' . $middlewareClassName );
		}
	}

	/**
	 * @param string $middlewareClassName
	 *
	 * @return MiddlewareClassName
	 * @throws InvalidArgumentException
	 */
	public static function new( string $middlewareClassName ) : self
	{
		return new self( trim( $middlewareClassName ) );
	}

	public function toString() : string
	{
		return $this->middlewareClassName;
	}

	public function equals( MiddlewareClassName $other ) : bool
	{
		return $other->middlewareClassName === $this->middlewareClassName;
	}

	#[Pure]
	public function __toString() : string
	{
		return $this->toString();
	}
}
