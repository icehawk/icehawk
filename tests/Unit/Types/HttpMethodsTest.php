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

		self::assertTrue( HttpMethod::get()->equalsOneOf( $methods->current() ) );

		$methods->next();

		self::assertTrue( HttpMethod::post()->equalsOneOf( $methods->current() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCount() : void
	{
		self::assertCount( 2, HttpMethods::new( HttpMethod::get(), HttpMethod::post() ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		self::assertCount( 0, HttpMethods::new() );
		self::assertCount( 1, HttpMethods::new( HttpMethod::get() ) );
		self::assertCount( 2, HttpMethods::new( HttpMethod::get(), HttpMethod::post() ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAdd() : void
	{
		$methods = HttpMethods::new();
		self::assertCount( 0, $methods );

		$methods->add( HttpMethod::get() );
		self::assertCount( 1, $methods );

		$methods->add( HttpMethod::post(), HttpMethod::head() );
		self::assertCount( 3, $methods );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAll() : void
	{
		self::assertCount( 9, HttpMethods::all() );

		self::assertTrue( HttpMethod::trace()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::connect()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::options()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::get()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::head()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::post()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::put()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::patch()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::delete()->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
	}
}
