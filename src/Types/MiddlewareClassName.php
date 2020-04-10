<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;
use function class_exists;
use function class_implements;
use function in_array;

final class MiddlewareClassName
{
	private string $middlewareClassName;

	/**
	 * @param string $middlewareClassName
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $middlewareClassName )
	{
		$this->guardMiddlewareClassNameIsValid( $middlewareClassName );

		$this->middlewareClassName = $middlewareClassName;
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

		$implementations = class_implements( $middlewareClassName );

		if ( !$implementations || !in_array( MiddlewareInterface::class, $implementations, true ) )
		{
			throw new InvalidArgumentException(
				'Middleware class does not implement '
				. MiddlewareInterface::class
				. ': '
				. $middlewareClassName
			);
		}
	}

	/**
	 * @param string $middlewareClassName
	 *
	 * @return MiddlewareClassName
	 * @throws InvalidArgumentException
	 */
	public static function newFromString( string $middlewareClassName ) : self
	{
		return new self( $middlewareClassName );
	}

	public function toString() : string
	{
		return $this->middlewareClassName;
	}
}
