<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Tests\Unit\Stubs\FallbackMiddleware;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Stubs\PassThroughMiddleware;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class MiddlewareClassNamesTest extends TestCase
{
	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testAdd() : void
	{
		$classNames = MiddlewareClassNames::new();
		self::assertCount( 0, $classNames );

		$classNames->add( MiddlewareClassName::new( MiddlewareImplementation::class ) );
		self::assertCount( 1, $classNames );

		$classNames->add(
			MiddlewareClassName::new( FallbackMiddleware::class ),
			MiddlewareClassName::new( PassThroughMiddleware::class )
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
			MiddlewareClassNames::newFromStrings(
				PassThroughMiddleware::class,
				MiddlewareImplementation::class
			)
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStrings() : void
	{
		self::assertEquals(
			MiddlewareClassNames::new(
				MiddlewareClassName::new( PassThroughMiddleware::class ),
				MiddlewareClassName::new( MiddlewareImplementation::class )
			),
			MiddlewareClassNames::newFromStrings(
				PassThroughMiddleware::class,
				MiddlewareImplementation::class
			)
		);
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testNew() : void
	{
		self::assertCount( 0, MiddlewareClassNames::new() );
		self::assertCount(
			1,
			MiddlewareClassNames::new(
				MiddlewareClassName::new( MiddlewareImplementation::class )
			)
		);
		self::assertCount(
			2,
			MiddlewareClassNames::new(
				MiddlewareClassName::new( PassThroughMiddleware::class ),
				MiddlewareClassName::new( MiddlewareImplementation::class )
			)
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetIterator() : void
	{
		$classNames = MiddlewareClassNames::newFromStrings(
			PassThroughMiddleware::class,
			MiddlewareImplementation::class
		)->getIterator();

		self::assertEquals(
			MiddlewareClassName::new( PassThroughMiddleware::class ),
			$classNames->current()
		);

		$classNames->next();

		self::assertEquals(
			MiddlewareClassName::new( MiddlewareImplementation::class ),
			$classNames->current()
		);

		$classNames->next();

		self::assertFalse( $classNames->valid() );
	}
}
