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
		$this->assertCount( 0, $classNames );

		$classNames->add( MiddlewareClassName::newFromString( MiddlewareImplementation::class ) );
		$this->assertCount( 1, $classNames );

		$classNames->add(
			MiddlewareClassName::newFromString( FallbackMiddleware::class ),
			MiddlewareClassName::newFromString( PassThroughMiddleware::class )
		);
		$this->assertCount( 3, $classNames );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testCount() : void
	{
		$this->assertCount(
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
		$this->assertEquals(
			MiddlewareClassNames::new(
				MiddlewareClassName::newFromString( PassThroughMiddleware::class ),
				MiddlewareClassName::newFromString( MiddlewareImplementation::class )
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
		$this->assertCount( 0, MiddlewareClassNames::new() );
		$this->assertCount(
			1,
			MiddlewareClassNames::new(
				MiddlewareClassName::newFromString( MiddlewareImplementation::class )
			)
		);
		$this->assertCount(
			2,
			MiddlewareClassNames::new(
				MiddlewareClassName::newFromString( PassThroughMiddleware::class ),
				MiddlewareClassName::newFromString( MiddlewareImplementation::class )
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

		$this->assertEquals(
			MiddlewareClassName::newFromString( PassThroughMiddleware::class ),
			$classNames->current()
		);

		$classNames->next();

		$this->assertEquals(
			MiddlewareClassName::newFromString( MiddlewareImplementation::class ),
			$classNames->current()
		);

		$classNames->next();

		$this->assertFalse( $classNames->valid() );
	}
}
