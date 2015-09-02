<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Requests;

use Fortuneglobe\IceHawk\RequestInfo;

class RequestInfoTest extends \PHPUnit_Framework_TestCase
{
	public function testCanInstantiateFromEnvUsingGlobalServerVariable()
	{
		$requestTimeFloat = microtime( true );

		$_SERVER['REQUEST_URI']        = '/unit/test';
		$_SERVER['REQUEST_METHOD']     = 'POST';
		$_SERVER['QUERY_STRING']       = 'unit=test';
		$_SERVER['HTTP_USER_AGENT'] = 'phpunit cli';
		$_SERVER['HTTP_HOST']          = 'www.icehawk.de';
		$_SERVER['HTTP_ACCEPT']        = 'text/html; charset=utf-8';
		$_SERVER['SERVER_ADDR']        = '127.0.0.1';
		$_SERVER['REMOTE_ADDR']        = '192.168.0.10';
		$_SERVER['REQUEST_TIME_FLOAT'] = $requestTimeFloat;
		$_SERVER['HTTPS'] = 'on';
		$_SERVER['PHP_AUTH_USER'] = 'Unit';
		$_SERVER['PHP_AUTH_PW'] = 'Test';

		$requestInfo = RequestInfo::fromEnv();

		$this->assertInstanceOf( RequestInfo::class, $requestInfo );

		$this->assertEquals( '/unit/test', $requestInfo->getUri() );
		$this->assertEquals( 'POST', $requestInfo->getMethod() );
		$this->assertEquals( 'unit=test', $requestInfo->getQueryString() );
		$this->assertEquals( 'phpunit cli', $requestInfo->getUserAgent() );
		$this->assertEquals( 'www.icehawk.de', $requestInfo->getHost() );
		$this->assertEquals( 'text/html; charset=utf-8', $requestInfo->getAcceptedContentTypes() );
		$this->assertEquals( '127.0.0.1', $requestInfo->getServerAddress() );
		$this->assertEquals( '192.168.0.10', $requestInfo->getClientAddress() );
		$this->assertEquals( $requestTimeFloat, $requestInfo->getRequestTimeFloat() );
		$this->assertEquals( 'Unit', $requestInfo->getAuthUser() );
		$this->assertEquals( 'Test', $requestInfo->getAuthPassword() );
		$this->assertEquals( $requestTimeFloat, $requestInfo->getRequestTimeFloat() );
		$this->assertTrue( $requestInfo->isSecure() );
	}

	public function testGettersReturnNullIfKeyIsNotSetInServerData()
	{
		$requestInfo = new RequestInfo( [ ] );

		$this->assertNull( $requestInfo->getUri() );
		$this->assertNull( $requestInfo->getMethod() );
		$this->assertNull( $requestInfo->getQueryString() );
		$this->assertNull( $requestInfo->getUserAgent() );
		$this->assertNull( $requestInfo->getHost() );
		$this->assertNull( $requestInfo->getAcceptedContentTypes() );
		$this->assertNull( $requestInfo->getServerAddress() );
		$this->assertNull( $requestInfo->getClientAddress() );
		$this->assertNull( $requestInfo->getRequestTimeFloat() );
	}

	public function testGetUriOnlyReturnsThePathWithoutQueryString()
	{
		$serverData  = [ 'REQUEST_URI' => '/unit/test?unit=test' ];
		$requestInfo = new RequestInfo( $serverData );

		$this->assertEquals( '/unit/test', $requestInfo->getUri() );
	}

	/**
	 * @dataProvider requestMethodProvider
	 */
	public function testGetMethodAlwaysReturnsRequestMethodInUpperCase( $requestMethod, $expectedRequestMethod )
	{
		$serverData  = [ 'REQUEST_METHOD' => $requestMethod ];
		$requestInfo = new RequestInfo( $serverData );

		$this->assertEquals( $expectedRequestMethod, $requestInfo->getMethod() );
	}

	public function requestMethodProvider()
	{
		return [
			[ 'get', 'GET' ],
			[ 'Get', 'GET' ],
			[ 'GeT', 'GET' ],
			[ 'post', 'POST' ],
			[ 'pOSt', 'POST' ],
		];
	}
}
