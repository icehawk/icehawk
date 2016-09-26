<?php declare(strict_types = 1);
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

class ReadRequestInputTest extends \PHPUnit_Framework_TestCase
{
	public function requestDataProvider()
	{
		return [
			[
				['unit' => 'test', 'test' => 'unit'],
				'unit',
				'test',
			],
			[
				['unit' => 'test', 'test' => 'unit'],
				'test',
				'unit',
			],
			[
				['unit' => ['test' => 'unit']],
				'unit',
				['test' => 'unit'],
			],
		];
	}

	/**
	 * @dataProvider requestDataProvider
	 */
	public function testGetDataReturnsProvidedGetData( array $getData )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertEquals( $getData, $readRequestInput->getData() );
	}

	/**
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $getData, $key, $expectedValue )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertEquals( $expectedValue, $readRequestInput->get( $key ) );
	}

	public function nullKeyDataProvider()
	{
		return [
			[
				['unit' => 'test', 'test' => 'unit'],
				'blubb',
			],
			[
				['unit' => 'test', 'test' => 'unit'],
				'blubb',
			],
			[
				['unit' => ['test' => 'unit']],
				'blubb',
			],
		];
	}

	/**
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $getData, $key )
	{
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertNull( $readRequestInput->get( $key ) );
	}
}
