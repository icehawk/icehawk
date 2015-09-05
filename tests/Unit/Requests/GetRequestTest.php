<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Requests;

use Fortuneglobe\IceHawk\Requests\GetRequest;

class GetRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $getData, $key, $expectedValue )
	{
		$getRequest = new GetRequest( $getData );

		$this->assertEquals( $expectedValue, $getRequest->get( $key ) );
	}

	public function requestDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit',
				'test'
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test',
				'unit'
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit',
				[ 'test' => 'unit' ]
			],
		];
	}

	/**
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $getData, $key )
	{
		$getRequest = new GetRequest( $getData );

		$this->assertNull( $getRequest->get( $key ) );
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
}
