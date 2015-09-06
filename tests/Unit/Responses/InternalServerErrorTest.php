<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\InternalServerError;

class InternalServerErrorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsPlainTextWithHttpCode500()
	{
		( new InternalServerError() )->respond();

		$this->assertContains( 'Content-Type: text/plain; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::INTERNAL_SERVER_ERROR, http_response_code() );
		$this->expectOutputString( "Internal server error." );
	}
}
