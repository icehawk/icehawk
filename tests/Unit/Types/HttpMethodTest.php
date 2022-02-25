<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpMethod;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use ValueError;

final class HttpMethodTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testHead() : void
	{
		self::assertSame( 'HEAD', HttpMethod::HEAD->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPut() : void
	{
		self::assertSame( 'PUT', HttpMethod::PUT->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testTrace() : void
	{
		self::assertSame( 'TRACE', HttpMethod::TRACE->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testToString() : void
	{
		self::assertSame( 'HEAD', HttpMethod::from( 'HEAD' )->toString() );
		self::assertSame( 'GET', HttpMethod::from( 'GET' )->toString() );
		self::assertSame( 'POST', HttpMethod::from( 'POST' )->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testOptions() : void
	{
		self::assertSame( 'OPTIONS', HttpMethod::OPTIONS->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPatch() : void
	{
		self::assertSame( 'PATCH', HttpMethod::PATCH->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testConnect() : void
	{
		self::assertSame( 'CONNECT', HttpMethod::CONNECT->toString() );
	}

	public function testNewFromStringThrowsExceptionForInvalidHttpMethod() : void
	{
		$this->expectException( ValueError::class );

		/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
		/** @noinspection UnusedFunctionResultInspection */
		HttpMethod::from( 'unknown' );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testDelete() : void
	{
		self::assertSame( 'DELETE', HttpMethod::DELETE->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testEquals() : void
	{
		self::assertTrue( HttpMethod::DELETE->equalsOneOf( HttpMethod::from( 'DELETE' ) ) );
		self::assertFalse( HttpMethod::DELETE->equalsOneOf( HttpMethod::from( 'POST' ) ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testEqualsOneOfMultiple() : void
	{
		self::assertTrue( HttpMethod::DELETE->equalsOneOf( HttpMethod::GET, HttpMethod::DELETE ) );
		self::assertFalse( HttpMethod::DELETE->equalsOneOf( HttpMethod::POST, HttpMethod::GET ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGet() : void
	{
		self::assertSame( 'GET', HttpMethod::GET->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPost() : void
	{
		self::assertSame( 'POST', HttpMethod::POST->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testEqualsString() : void
	{
		self::assertTrue( HttpMethod::from( 'HEAD' )->equalsString( 'HEAD' ) );
		self::assertTrue( HttpMethod::from( 'GET' )->equalsString( 'get' ) );
		self::assertTrue( HttpMethod::POST->equalsString( 'POsT' ) );
	}
}
