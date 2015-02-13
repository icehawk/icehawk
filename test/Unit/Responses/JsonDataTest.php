<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Responses;

use Fortuneglobe\IceHawk\Responses\JsonData;

class JsonDataTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @runInSeparateProcess
	 * @dataProvider jsonDataProvider
	 */
	public function testRespondesJson( $data, $expectedJson )
	{
		( new JsonData( $data ) )->respond();

		$this->assertContains( 'Content-Type: application/json; charset=utf-8', xdebug_get_headers() );
		$this->expectOutputString( $expectedJson );
	}

	public function jsonDataProvider()
	{
		return [
			[ 'string', '"string"' ],
			[ new \stdClass(), '{}' ],
			[ [ 'unit', 'test' ], '["unit","test"]' ],
			[ [ 'unit' => 'test' ], '{"unit":"test"}' ],
		];
	}
}
