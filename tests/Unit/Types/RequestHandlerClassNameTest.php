<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Stubs\RequestHandlerImplementation;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandlerClassNameTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testToString() : void
	{
		$this->assertSame(
			RequestHandlerImplementation::class,
			RequestHandlerClassName::newFromString( RequestHandlerImplementation::class )->toString()
		);
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForNonExistingClass() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Request handler class does not exist: Foo\Bar\RequestHandler' );

		RequestHandlerClassName::newFromString( 'Foo\Bar\RequestHandler' );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForClassThatDoesNotImplementRequestHandlerInterface() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Request handler class does not implement ' . RequestHandlerInterface::class . ': ' . self::class
		);

		RequestHandlerClassName::newFromString( self::class );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEquals() : void
	{
		$className1 = RequestHandlerClassName::newFromString( RequestHandlerImplementation::class );
		$className2 = RequestHandlerClassName::newFromString( RequestHandlerImplementation::class );
		$className3 = RequestHandlerClassName::newFromString( FallbackRequestHandler::class );

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
		$className1 = RequestHandlerClassName::newFromString( RequestHandlerImplementation::class );

		$this->assertTrue( $className1->equalsString( RequestHandlerImplementation::class ) );
		$this->assertFalse( $className1->equalsString( FallbackRequestHandler::class ) );
	}
}
