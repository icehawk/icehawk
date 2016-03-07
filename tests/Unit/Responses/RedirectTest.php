<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Responses\Redirect;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 * @dataProvider locationCodeProvider
	 */
	public function testSendsLocationAndHttpCodeHeaderWhenResponding(
		$location, $httpCode,
		$expectedHeader, $expectedCode
	)
	{
		$redirect = new Redirect( $location, $httpCode );

		$redirect->respond();

		$this->assertContains( $expectedHeader, xdebug_get_headers() );
		$this->assertEquals( $expectedCode, http_response_code() );
	}

	public function locationCodeProvider()
	{
		return [
			[ '/unit/test', null, 'Location: /unit/test', HttpCode::MOVED_PERMANENTLY ],
			[ '/unit/test', HttpCode::MOVED_PERMANENTLY, 'Location: /unit/test', HttpCode::MOVED_PERMANENTLY ],
			[ '/unit/test', HttpCode::FOUND, 'Location: /unit/test', HttpCode::FOUND ],
		];
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionIsClosedWhenResponded()
	{
		$redirect = new Redirect( '/unit/test', HttpCode::MOVED_PERMANENTLY );

		session_start();

		$this->assertEquals( PHP_SESSION_ACTIVE, session_status() );

		$redirect->respond();

		$this->assertEquals( PHP_SESSION_NONE, session_status() );
	}
}
