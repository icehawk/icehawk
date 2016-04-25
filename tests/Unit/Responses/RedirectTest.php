<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Responses\Redirect;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
	public function locationCodeProvider()
	{
		return [
			[ '/unit/test', HttpCode::MOVED_PERMANENTLY, 'Location: /unit/test', HttpCode::MOVED_PERMANENTLY ],
			[ '/unit/test', HttpCode::TEMPORARY_REDIRECT, 'Location: /unit/test', HttpCode::TEMPORARY_REDIRECT ],
		];
	}
	
	/**
	 * @runInSeparateProcess
	 * @dataProvider locationCodeProvider
	 */
	public function testSendsLocationAndHttpCodeHeaderWhenResponding(
		$location, $httpCode, $expectedHeader, $expectedCode
	)
	{
		$redirect = new Redirect( $location, $httpCode );

		$redirect->respond();

		$reflection = new \ReflectionClass( '\\Fortuneglobe\\IceHawk\\Responses\\Redirect' );
		$getBody    = $reflection->getMethod( 'getBody' );
		$getBody->setAccessible( true );

		$expectedBody = $getBody->invoke( $redirect );

		$this->expectOutputString( $expectedBody );
		$this->assertContains( $expectedHeader, xdebug_get_headers() );
		$this->assertEquals( $expectedCode, http_response_code() );
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

		$reflection = new \ReflectionClass( '\\Fortuneglobe\\IceHawk\\Responses\\Redirect' );
		$getBody    = $reflection->getMethod( 'getBody' );
		$getBody->setAccessible( true );

		$expectedBody = $getBody->invoke( $redirect );

		$this->expectOutputString( $expectedBody );
	}
}
