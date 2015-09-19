<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Responses\Forbidden;

class ForbiddenTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsPlainTextWithHttpCode403()
	{
		( new Forbidden() )->respond();

		$this->assertContains( 'Content-Type: text/plain; charset=utf-8', xdebug_get_headers() );
		$this->assertEquals( Http::FORBIDDEN, http_response_code() );
		$this->expectOutputString( "Forbidden!" );
	}
}
