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
		$this->assertSame( 'HEAD', HttpMethod::head()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPut() : void
	{
		$this->assertSame( 'PUT', HttpMethod::put()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testTrace() : void
	{
		$this->assertSame( 'TRACE', HttpMethod::trace()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testToString() : void
	{
		$this->assertSame( 'HEAD', HttpMethod::newFromString( 'head' )->toString() );
		$this->assertSame( 'GET', HttpMethod::newFromString( 'Get' )->toString() );
		$this->assertSame( 'POST', HttpMethod::newFromString( 'poST' )->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testOptions() : void
	{
		$this->assertSame( 'OPTIONS', HttpMethod::options()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPatch() : void
	{
		$this->assertSame( 'PATCH', HttpMethod::patch()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testConnect() : void
	{
		$this->assertSame( 'CONNECT', HttpMethod::connect()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testNewFromString() : void
	{
		$this->assertSame( 'HEAD', HttpMethod::newFromString( 'head' )->toString() );
		$this->assertSame( 'GET', HttpMethod::newFromString( 'Get' )->toString() );
		$this->assertSame( 'POST', HttpMethod::newFromString( 'poST' )->toString() );
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
		$this->assertSame( 'DELETE', HttpMethod::delete()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEquals() : void
	{
		$this->assertTrue( HttpMethod::delete()->equalsOneOf( HttpMethod::newFromString( 'delete' ) ) );
		$this->assertFalse( HttpMethod::delete()->equalsOneOf( HttpMethod::newFromString( 'post' ) ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testEqualsOneOfMultiple() : void
	{
		$this->assertTrue( HttpMethod::delete()->equalsOneOf( HttpMethod::get(), HttpMethod::delete() ) );
		$this->assertFalse( HttpMethod::delete()->equalsOneOf( HttpMethod::post(), HttpMethod::get() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGet() : void
	{
		$this->assertSame( 'GET', HttpMethod::get()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testPost() : void
	{
		$this->assertSame( 'POST', HttpMethod::post()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testCanCastInstanceToString() : void
	{
		$this->assertSame( 'HEAD', (string)HttpMethod::newFromString( 'head' ) );
		$this->assertSame( 'GET', (string)HttpMethod::newFromString( 'Get' ) );
		$this->assertSame( 'POST', (string)HttpMethod::newFromString( 'poST' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEqualsString() : void
	{
		$this->assertTrue( HttpMethod::newFromString( 'head' )->equalsString( 'HEAD' ) );
		$this->assertTrue( HttpMethod::newFromString( 'Get' )->equalsString( 'get' ) );
		$this->assertTrue( HttpMethod::post()->equalsString( 'POsT' ) );
	}
}
