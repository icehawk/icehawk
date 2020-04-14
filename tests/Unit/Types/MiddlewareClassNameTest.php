<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Tests\Unit\Stubs\FallbackMiddleware;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
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

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEquals() : void
	{
		$className1 = MiddlewareClassName::newFromString( MiddlewareImplementation::class );
		$className2 = MiddlewareClassName::newFromString( MiddlewareImplementation::class );
		$className3 = MiddlewareClassName::newFromString( FallbackMiddleware::class );

		$this->assertTrue( $className1->equals( $className2 ) );
		$this->assertTrue( $className2->equals( $className1 ) );
		$this->assertFalse( $className1->equals( $className3 ) );
		$this->assertFalse( $className2->equals( $className3 ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEqualsString() : void
	{
		$className1 = MiddlewareClassName::newFromString( MiddlewareImplementation::class );

		$this->assertTrue( $className1->equalsString( MiddlewareImplementation::class ) );
		$this->assertFalse( $className1->equalsString( FallbackMiddleware::class ) );
	}
}
