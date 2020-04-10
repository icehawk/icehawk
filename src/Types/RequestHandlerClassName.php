<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use Psr\Http\Server\RequestHandlerInterface;
use function class_exists;
use function class_implements;
use function in_array;

final class RequestHandlerClassName
{
	private string $requestHandlerClassName;

	/**
	 * @param string $requestHandlerClassName
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $requestHandlerClassName )
	{
		$this->guardRequestHandlerClassNameIsValid( $requestHandlerClassName );

		$this->requestHandlerClassName = $requestHandlerClassName;
	}

	/**
	 * @param string $requestHandlerClassName
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardRequestHandlerClassNameIsValid( string $requestHandlerClassName ) : void
	{
		if ( !class_exists( $requestHandlerClassName ) )
		{
			throw new InvalidArgumentException( 'Request handler class does not exist: ' . $requestHandlerClassName );
		}

		$implementations = class_implements( $requestHandlerClassName );

		if ( !$implementations || !in_array( RequestHandlerInterface::class, $implementations, true ) )
		{
			throw new InvalidArgumentException(
				'Request handler class does not implement '
				. RequestHandlerInterface::class
				. ': '
				. $requestHandlerClassName
			);
		}
	}

	/**
	 * @param string $requestHandlerClassName
	 *
	 * @return RequestHandlerClassName
	 * @throws InvalidArgumentException
	 */
	public static function newFromString( string $requestHandlerClassName ) : self
	{
		return new self( $requestHandlerClassName );
	}

	public function toString() : string
	{
		return $this->requestHandlerClassName;
	}
}
