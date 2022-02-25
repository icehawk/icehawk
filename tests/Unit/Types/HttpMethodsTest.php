<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\HttpMethods;
use Iterator;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class HttpMethodsTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws \Exception
	 */
	public function testGetIterator() : void
	{
		/** @var Iterator<int, HttpMethod> $methods */
		$methods = HttpMethods::new( HttpMethod::GET, HttpMethod::POST )->getIterator();

		self::assertTrue( HttpMethod::GET->equalsOneOf( $methods->current() ) );

		$methods->next();

		self::assertTrue( HttpMethod::POST->equalsOneOf( $methods->current() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCount() : void
	{
		self::assertCount( 2, HttpMethods::new( HttpMethod::GET, HttpMethod::POST ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		self::assertCount( 0, HttpMethods::new() );
		self::assertCount( 1, HttpMethods::new( HttpMethod::GET ) );
		self::assertCount( 2, HttpMethods::new( HttpMethod::GET, HttpMethod::POST ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testAdd() : void
	{
		$methods = HttpMethods::new();
		self::assertCount( 0, $methods );

		$methods->add( HttpMethod::GET );
		self::assertCount( 1, $methods );

		$methods->add( HttpMethod::POST, HttpMethod::HEAD );
		self::assertCount( 3, $methods );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws \Exception
	 */
	public function testAll() : void
	{
		self::assertCount( 9, HttpMethods::all() );

		self::assertTrue( HttpMethod::TRACE->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::CONNECT->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::OPTIONS->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::GET->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::HEAD->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::POST->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::PUT->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::PATCH->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
		self::assertTrue( HttpMethod::DELETE->equalsOneOf( ...HttpMethods::all()->getIterator() ) );
	}
}
