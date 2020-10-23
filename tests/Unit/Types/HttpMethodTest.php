<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class HttpMethodTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testHead() : void
	{
		self::assertSame( 'HEAD', HttpMethod::head()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPut() : void
	{
		self::assertSame( 'PUT', HttpMethod::put()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testTrace() : void
	{
		self::assertSame( 'TRACE', HttpMethod::trace()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testToString() : void
	{
		self::assertSame( 'HEAD', HttpMethod::newFromString( 'head' )->toString() );
		self::assertSame( 'GET', HttpMethod::newFromString( 'Get' )->toString() );
		self::assertSame( 'POST', HttpMethod::newFromString( 'poST' )->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testOptions() : void
	{
		self::assertSame( 'OPTIONS', HttpMethod::options()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPatch() : void
	{
		self::assertSame( 'PATCH', HttpMethod::patch()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testConnect() : void
	{
		self::assertSame( 'CONNECT', HttpMethod::connect()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testNewFromString() : void
	{
		self::assertSame( 'HEAD', HttpMethod::newFromString( 'head' )->toString() );
		self::assertSame( 'GET', HttpMethod::newFromString( 'Get' )->toString() );
		self::assertSame( 'POST', HttpMethod::newFromString( 'poST' )->toString() );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionForInvalidHttpMethod() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid value for HttpMethod: unknown' );

		HttpMethod::newFromString( 'unknown' );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testDelete() : void
	{
		self::assertSame( 'DELETE', HttpMethod::delete()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEquals() : void
	{
		self::assertTrue( HttpMethod::delete()->equalsOneOf( HttpMethod::newFromString( 'delete' ) ) );
		self::assertFalse( HttpMethod::delete()->equalsOneOf( HttpMethod::newFromString( 'post' ) ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testEqualsOneOfMultiple() : void
	{
		self::assertTrue( HttpMethod::delete()->equalsOneOf( HttpMethod::get(), HttpMethod::delete() ) );
		self::assertFalse( HttpMethod::delete()->equalsOneOf( HttpMethod::post(), HttpMethod::get() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGet() : void
	{
		self::assertSame( 'GET', HttpMethod::get()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPost() : void
	{
		self::assertSame( 'POST', HttpMethod::post()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testCanCastInstanceToString() : void
	{
		self::assertSame( 'HEAD', (string)HttpMethod::newFromString( 'head' ) );
		self::assertSame( 'GET', (string)HttpMethod::newFromString( 'Get' ) );
		self::assertSame( 'POST', (string)HttpMethod::newFromString( 'poST' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEqualsString() : void
	{
		self::assertTrue( HttpMethod::newFromString( 'head' )->equalsString( 'HEAD' ) );
		self::assertTrue( HttpMethod::newFromString( 'Get' )->equalsString( 'get' ) );
		self::assertTrue( HttpMethod::post()->equalsString( 'POsT' ) );
	}
}
