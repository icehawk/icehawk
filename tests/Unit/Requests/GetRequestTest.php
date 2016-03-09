<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Requests;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;

class GetRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param array  $getData
	 * @param string $key
	 * @param string $expectedValue
	 *
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $getData, $key, $expectedValue )
	{
		$getRequest = new ReadRequest( RequestInfo::fromEnv(), $getData );

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
	 * @param array  $getData
	 * @param string $key
	 *
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $getData, $key )
	{
		$getRequest = new ReadRequest( RequestInfo::fromEnv(), $getData );

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
