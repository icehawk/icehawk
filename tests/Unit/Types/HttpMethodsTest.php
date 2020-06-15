<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\HttpMethods;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class HttpMethodsTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testGetIterator() : void
	{
		$methods = HttpMethods::new( HttpMethod::get(), HttpMethod::post() )->getIterator();

		$this->assertTrue( HttpMethod::get()->equalsOneOf( $methods->current() ) );

		$methods->next();

		$this->assertTrue( HttpMethod::post()->equalsOneOf( $methods->current() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCount() : void
	{
		$this->assertCount( 2, HttpMethods::new( HttpMethod::get(), HttpMethod::post() ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		$this->assertCount( 0, HttpMethods::new() );
		$this->assertCount( 1, HttpMethods::new( HttpMethod::get() ) );
		$this->assertCount( 2, HttpMethods::new( HttpMethod::get(), HttpMethod::post() ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAdd() : void
	{
		$methods = HttpMethods::new();
		$this->assertCount( 0, $methods );

		$methods->add( HttpMethod::get() );
		$this->assertCount( 1, $methods );

		$methods->add( HttpMethod::post(), HttpMethod::head() );
		$this->assertCount( 3, $methods );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAll() : void
	{
		$this->assertCount( 9, HttpMethods::all() );

		$this->assertTrue( HttpMethod::trace()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::connect()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::options()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::get()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::head()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::post()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::put()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::patch()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		$this->assertTrue( HttpMethod::delete()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
	}
}
