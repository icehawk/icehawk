<?php declare(strict_types=1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Requests;

use IceHawk\IceHawk\Requests\ReadRequestInput;
use PHPUnit\Framework\TestCase;

class ReadRequestInputTest extends TestCase
{

	/**
	 * @dataProvider requestDataProvider
	 *
	 * @param array $getData
	 */
	public function testGetDataReturnsProvidedGetData( array $getData )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertEquals( $getData, $readRequestInput->getData() );
	}

	public function requestDataProvider() : array
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit',
				'test',
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test',
				'unit',
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit',
				[ 'test' => 'unit' ],
			],
			[
				[ 'unit' => '' ],
				'unit',
				'',
			],
		];
	}

	/**
	 * @dataProvider requestDataProvider
	 *
	 * @param array  $getData
	 * @param string $key
	 * @param mixed  $expectedValue
	 */
	public function testCanGetRequestValueByKey( array $getData, string $key, $expectedValue )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertSame( $expectedValue, $readRequestInput->get( $key ) );
	}

	/**
	 * @dataProvider nullKeyDataProvider
	 *
	 * @param array  $getData
	 * @param string $key
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $getData, string $key )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertNull( $readRequestInput->get( $key ) );
	}

	public function nullKeyDataProvider() : array
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'blubb',
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'blubb',
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'blubb',
			],
		];
	}

	public function testGetterReturnsDefaultValueIfProvidedAndKeyIsNotFound()
	{
		$readRequestInput = new ReadRequestInput( [ 'unit' => 'test' ] );

		$stdObj = new \stdClass();

		$this->assertSame( [ '123' ], $readRequestInput->get( 'someKey', [ '123' ] ) );
		$this->assertSame( '123', $readRequestInput->get( 'someKey', '123' ) );
		$this->assertSame( 123, $readRequestInput->get( 'someKey', 123 ) );
		$this->assertSame( $stdObj, $readRequestInput->get( 'someKey', $stdObj ) );
		$this->assertNull( $readRequestInput->get( 'someKey', null ) );
		$this->assertNull( $readRequestInput->get( 'someKey' ) );

		$this->assertSame( 'test', $readRequestInput->get( 'unit', [ '123' ] ) );
	}
}
