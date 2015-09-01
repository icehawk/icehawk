<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;

class MethodNotAllowedTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsPlainTextWithHttpCode405()
	{
		( new MethodNotAllowed() )->respond();

		$this->assertContains( 'Content-Type: text/plain; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::METHOD_NOT_ALLOWED, http_response_code() );
		$this->expectOutputString( 'Method not allowed.' );
	}
}
