<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Requests;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Requests\ReadRequest;

class ReadRequestTest extends \PHPUnit_Framework_TestCase
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
		$readRequestInput = new ReadRequest( RequestInfo::fromEnv(), $getData );

		$this->assertEquals( $getData, $readRequestInput->getInputData() );
	}

	/**
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $getData, $key, $expectedValue )
	{
		$readRequestInput = new ReadRequest( RequestInfo::fromEnv(), $getData );

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
		$readRequestInput = new ReadRequest( RequestInfo::fromEnv(), $getData );

		$this->assertNull( $readRequestInput->get( $key ) );
	}
}
