<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Tests\Unit\Stubs\FallbackMiddleware;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class MiddlewareClassNameTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testToString() : void
	{
		self::assertSame(
			MiddlewareImplementation::class,
			MiddlewareClassName::new( MiddlewareImplementation::class )->toString()
		);
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForNonExistingClass() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Middleware class does not exist: Foo\Bar\Middleware' );

		MiddlewareClassName::new( 'Foo\Bar\Middleware' );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEquals() : void
	{
		$className1 = MiddlewareClassName::new( MiddlewareImplementation::class );
		$className2 = MiddlewareClassName::new( MiddlewareImplementation::class );
		$className3 = MiddlewareClassName::new( FallbackMiddleware::class );

		self::assertTrue( $className1->equals( $className2 ) );
		self::assertTrue( $className2->equals( $className1 ) );
		self::assertFalse( $className1->equals( $className3 ) );
		self::assertFalse( $className2->equals( $className3 ) );
	}
}
