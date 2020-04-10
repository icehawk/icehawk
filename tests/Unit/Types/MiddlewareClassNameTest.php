<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Tests\Unit\Types\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareClassNameTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testToString() : void
	{
		$this->assertSame(
			MiddlewareImplementation::class,
			MiddlewareClassName::newFromString( MiddlewareImplementation::class )->toString()
		);
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForNonExistingClass() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Middleware class does not exist: Foo\Bar\Middleware' );

		MiddlewareClassName::newFromString( 'Foo\Bar\Middleware' );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForClassThatDoesNotImplementMiddlewareInterface() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Middleware class does not implement ' . MiddlewareInterface::class . ': ' . self::class
		);

		MiddlewareClassName::newFromString( self::class );
	}
}
