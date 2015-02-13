<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\BadRequest;

class BadRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsPlainTextWithHttpCode500()
	{
		$messages = [
			'This is a unit test.',
			'I am testing the output.'
		];

		( new BadRequest( $messages ) )->respond();

		$this->assertContains( 'Content-Type: text/plain; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::BAD_REQUEST, http_response_code() );
		$this->expectOutputString( "This is a unit test.\nI am testing the output." );
	}
}
