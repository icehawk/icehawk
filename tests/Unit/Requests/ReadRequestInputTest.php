<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Requests;

use Fortuneglobe\IceHawk\Requests\ReadRequestInput;

class ReadRequestInputTest extends \PHPUnit_Framework_TestCase
{
	public function requestDataProvider()
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

	/**
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $getData, $key )
	{		
		$readRequestInput = new ReadRequestInput( $getData );

		$this->assertNull( $readRequestInput->get( $key ) );
	}
}
