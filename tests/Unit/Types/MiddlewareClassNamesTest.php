<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Tests\Unit\Stubs\FallbackMiddleware;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Stubs\PassThroughMiddleware;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class MiddlewareClassNamesTest extends TestCase
{
	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAdd() : void
	{
		$classNames = MiddlewareClassNames::new();
		self::assertCount( 0, $classNames );

		$classNames->add( MiddlewareImplementation::class );
		self::assertCount( 1, $classNames );

		$classNames->add(
			FallbackMiddleware::class,
			PassThroughMiddleware::class
		);
		self::assertCount( 3, $classNames );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testCount() : void
	{
		self::assertCount(
			2,
			MiddlewareClassNames::new(
				PassThroughMiddleware::class,
				MiddlewareImplementation::class
			)
		);
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		self::assertCount( 0, MiddlewareClassNames::new() );
		self::assertCount( 1, MiddlewareClassNames::new( MiddlewareImplementation::class ) );
		self::assertCount(
			2,
			MiddlewareClassNames::new(
				PassThroughMiddleware::class,
				MiddlewareImplementation::class
			)
		);
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGetIterator() : void
	{
		$classNames = MiddlewareClassNames::new(
			PassThroughMiddleware::class,
			MiddlewareImplementation::class
		)->getIterator();

		self::assertSame(
			PassThroughMiddleware::class,
			$classNames->current()
		);

		$classNames->next();

		self::assertSame(
			MiddlewareImplementation::class,
			$classNames->current()
		);

		$classNames->next();

		self::assertFalse( $classNames->valid() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws \Exception
	 */
	public function testAppend() : void
	{
		$classNames = MiddlewareClassNames::new(
			PassThroughMiddleware::class,
			MiddlewareImplementation::class
		);

		self::assertCount( 2, $classNames );

		$appended = $classNames->append( $classNames );

		self::assertCount( 4, $appended );
		self::assertNotSame( $classNames, $appended );

		$expectedMiddlewares = [
			PassThroughMiddleware::class,
			MiddlewareImplementation::class,
			PassThroughMiddleware::class,
			MiddlewareImplementation::class,
		];

		self::assertSame( $expectedMiddlewares, iterator_to_array( $appended->getIterator(), false ) );
	}
}
