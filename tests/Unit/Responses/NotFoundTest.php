<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\NotFound;

class NotFoundTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsPlainTextWithHttpCode404()
	{
		( new NotFound() )->respond();

		$this->assertContains( 'Content-Type: text/plain; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::NOT_FOUND, http_response_code() );
		$this->expectOutputString( 'Not Found.' );
	}
}
