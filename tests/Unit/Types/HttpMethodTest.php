<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class HttpMethodTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testHead() : void
	{
		$this->assertSame( 'HEAD', HttpMethod::head()->toString() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testPut() : void
	{
		$this->assertSame( 'PUT', HttpMethod::put()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
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
	 * @throws InvalidArgumentException
	 */
	public function testOptions() : void
	{
		$this->assertSame( 'OPTIONS', HttpMethod::options()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testPatch() : void
	{
		$this->assertSame( 'PATCH', HttpMethod::patch()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
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
	 * @throws InvalidArgumentException
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
		$this->assertTrue( HttpMethod::delete()->equals( HttpMethod::newFromString( 'delete' ) ) );
		$this->assertFalse( HttpMethod::delete()->equals( HttpMethod::newFromString( 'post' ) ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testEqualsOneOfMultiple() : void
	{
		$this->assertTrue( HttpMethod::delete()->equals( HttpMethod::get(), HttpMethod::delete() ) );
		$this->assertFalse( HttpMethod::delete()->equals( HttpMethod::post(), HttpMethod::get() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGet() : void
	{
		$this->assertSame( 'GET', HttpMethod::get()->toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testPost() : void
	{
		$this->assertSame( 'POST', HttpMethod::post()->toString() );
	}
}
