<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Dependencies;

use IceHawk\IceHawk\Dependencies\Container;
use IceHawk\IceHawk\Dependencies\Exceptions\EntryNotFoundExcption;
use IceHawk\IceHawk\Dependencies\Exceptions\RetrievingEntryFailedException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class ContainerTest extends TestCase
{
	/**
	 * @throws EntryNotFoundExcption
	 * @throws RetrievingEntryFailedException
	 */
	public function testGetThrowsNotFoundException() : void
	{
		$this->expectException( EntryNotFoundExcption::class );
		$this->expectExceptionMessage( 'Could not find entry in container for ID: test-id' );

		Container::new()->get( 'test-id' );
	}

	/**
	 * @throws EntryNotFoundExcption
	 * @throws RetrievingEntryFailedException
	 */
	public function testGetThrowsRetrievingEntryFailedException() : void
	{
		$this->expectException( RetrievingEntryFailedException::class );
		$this->expectExceptionMessage( 'Could not retrieve entry from container for ID: test-id - Nope' );

		$container = Container::new();
		$container->register( 'test-id', fn() => throw new RuntimeException( 'Nope' ) );

		$container->get( 'test-id' );
	}

	/**
	 * @throws EntryNotFoundExcption
	 * @throws ExpectationFailedException
	 * @throws RetrievingEntryFailedException
	 * @throws Exception
	 */
	public function testGet() : void
	{
		$container = Container::new();
		$container->register( 'test-id', fn() => new stdClass() );

		self::assertTrue( $container->has( 'test-id' ) );
		self::assertInstanceOf( stdClass::class, $container->get( 'test-id' ) );
	}

	/**
	 * @throws EntryNotFoundExcption
	 * @throws ExpectationFailedException
	 * @throws RetrievingEntryFailedException
	 * @throws Exception
	 */
	public function testGetReturnsAlwaysTheSameInstance() : void
	{
		$container = Container::new();
		$container->register( 'test-id', fn() => new stdClass() );

		$objectFirstCall  = $container->get( 'test-id' );
		$objectSecondCall = $container->get( 'test-id' );

		self::assertInstanceOf( stdClass::class, $objectFirstCall );
		self::assertSame( $objectFirstCall, $objectSecondCall );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testHas() : void
	{
		self::assertFalse( Container::new()->has( 'test-id' ) );
		self::assertTrue( Container::new( ['test-id' => fn() => new stdClass()] )->has( 'test-id' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testRegister() : void
	{
		$container = Container::new();

		self::assertFalse( $container->has( 'test-id' ) );

		$container->register( 'test-id', fn() => new stdClass() );

		self::assertTrue( $container->has( 'test-id' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		$container = Container::new();

		self::assertFalse( $container->has( 'test-id' ) );

		$container = Container::new( ['test-id' => fn() => new stdClass()] );

		self::assertTrue( $container->has( 'test-id' ) );
	}
}
