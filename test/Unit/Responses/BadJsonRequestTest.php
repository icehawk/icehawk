<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;

class BadJsonRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsJsonWithHttpCode500()
	{
		$messages = [
			'This is a unit test.',
			'I am testing the output.'
		];

		( new BadJsonRequest( $messages ) )->respond();

		$this->assertContains( 'Content-Type: application/json; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::BAD_REQUEST, http_response_code() );
		$this->expectOutputString( '{"messages":["This is a unit test.","I am testing the output."]}' );
	}
}
