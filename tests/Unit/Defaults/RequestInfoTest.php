<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Defaults;

use IceHawk\IceHawk\Defaults\RequestInfo;

class RequestInfoTest extends \PHPUnit_Framework_TestCase
{
	public function testCanInstantiateFromEnvUsingGlobalServerVariable()
	{
		$requestTimeFloat = microtime( true );

		$_SERVER['REQUEST_URI']        = '/unit/test';
		$_SERVER['REQUEST_METHOD']     = 'POST';
		$_SERVER['QUERY_STRING']       = 'unit=test';
		$_SERVER['HTTP_USER_AGENT']    = 'phpunit cli';
		$_SERVER['HTTP_HOST']          = 'www.icehawk.de';
		$_SERVER['HTTP_REFERER']       = 'http://www.example.com';
		$_SERVER['HTTP_ACCEPT']        = 'text/html; charset=utf-8';
		$_SERVER['SERVER_ADDR']        = '127.0.0.1';
		$_SERVER['REMOTE_ADDR']        = '192.168.0.10';
		$_SERVER['HTTP_REFERER']       = 'http://www.example.com';
		$_SERVER['REQUEST_TIME_FLOAT'] = $requestTimeFloat;
		$_SERVER['HTTPS']              = 'on';
		$_SERVER['PHP_AUTH_USER']      = 'Unit';
		$_SERVER['PHP_AUTH_PW']        = 'Test';
		$_SERVER['CONTENT_LENGTH']     = '512';
		$_SERVER['CONTENT_TYPE']       = 'application/json';

		$requestInfo = RequestInfo::fromEnv();

		$this->assertInstanceOf( RequestInfo::class, $requestInfo );

		$this->assertEquals( '/unit/test', $requestInfo->getUri() );
		$this->assertEquals( 'POST', $requestInfo->getMethod() );
		$this->assertEquals( 'unit=test', $requestInfo->getQueryString() );
		$this->assertEquals( 'phpunit cli', $requestInfo->getUserAgent() );
		$this->assertEquals( 'www.icehawk.de', $requestInfo->getHost() );
		$this->assertEquals( 'http://www.example.com', $requestInfo->getReferer() );
		$this->assertEquals( 'text/html; charset=utf-8', $requestInfo->getAcceptedContentTypes() );
		$this->assertEquals( '127.0.0.1', $requestInfo->getServerAddress() );
		$this->assertEquals( '192.168.0.10', $requestInfo->getClientAddress() );
		$this->assertEquals( 'http://www.example.com', $requestInfo->getReferer() );
		$this->assertEquals( $requestTimeFloat, $requestInfo->getRequestTimeFloat() );
		$this->assertEquals( 'Unit', $requestInfo->getAuthUser() );
		$this->assertEquals( 'Test', $requestInfo->getAuthPassword() );
		$this->assertEquals( $requestTimeFloat, $requestInfo->getRequestTimeFloat() );
		$this->assertEquals( '512', $requestInfo->getContentLength() );
		$this->assertEquals( 'application/json', $requestInfo->getContentType() );
		$this->assertTrue( $requestInfo->isSecure() );
	}

	public function testGettersReturnEmptyValuesIfKeyIsNotSetInServerData()
	{
		$requestInfo = new RequestInfo( [] );

		$this->assertSame( '', $requestInfo->getMethod() );
		$this->assertSame( '', $requestInfo->getQueryString() );
		$this->assertSame( '', $requestInfo->getUserAgent() );
		$this->assertSame( '', $requestInfo->getHost() );
		$this->assertSame( '', $requestInfo->getReferer() );
		$this->assertSame( '', $requestInfo->getAcceptedContentTypes() );
		$this->assertSame( '', $requestInfo->getServerAddress() );
		$this->assertSame( '', $requestInfo->getClientAddress() );
		$this->assertSame( '', $requestInfo->getReferer() );
		$this->assertSame( '', $requestInfo->getAuthUser() );
		$this->assertSame( '', $requestInfo->getAuthPassword() );
		$this->assertSame( 0.0, $requestInfo->getRequestTimeFloat() );
	}

	public function testGetUriReturnSlashIfKeyIsNotSetInServerData()
	{
		$requestInfo = new RequestInfo( [] );

		$this->assertEquals( '/', $requestInfo->getUri() );
	}

	public function testGetUriOnlyReturnsThePathWithoutQueryString()
	{
		$serverData  = ['REQUEST_URI' => '/unit/test?unit=test'];
		$requestInfo = new RequestInfo( $serverData );

		$this->assertEquals( '/unit/test', $requestInfo->getUri() );
	}

	/**
	 * @dataProvider requestMethodProvider
	 */
	public function testGetMethodAlwaysReturnsRequestMethodInUpperCase( $requestMethod, $expectedRequestMethod )
	{
		$serverData  = ['REQUEST_METHOD' => $requestMethod];
		$requestInfo = new RequestInfo( $serverData );

		$this->assertEquals( $expectedRequestMethod, $requestInfo->getMethod() );
	}

	public function requestMethodProvider()
	{
		return [
			['get', 'GET'],
			['Get', 'GET'],
			['GeT', 'GET'],
			['head', 'HEAD'],
			['HeaD', 'HEAD'],
			['post', 'POST'],
			['pOSt', 'POST'],
		];
	}

	public function testHttpsIsCheckedCaseInsensitive()
	{
		$requestInfo1 = new RequestInfo( ['HTTPS' => 'On'] );
		$requestInfo2 = new RequestInfo( ['HTTPS' => 'on'] );
		$requestInfo3 = new RequestInfo( ['HTTPS' => 'oN'] );
		$requestInfo4 = new RequestInfo( ['HTTPS' => 'ON'] );

		$this->assertTrue( $requestInfo1->isSecure() );
		$this->assertTrue( $requestInfo2->isSecure() );
		$this->assertTrue( $requestInfo3->isSecure() );
		$this->assertTrue( $requestInfo4->isSecure() );
	}

	public function testCanGetCustomValue()
	{
		$requestInfo = new RequestInfo(
			[
				'custom1' => 'value',
				'custom2' => 123,
				'custom3' => 12.3,
				'custom4' => false,
				'custom5' => true,
				'custom6' => null,
			]
		);

		$this->assertSame( 'value', $requestInfo->getCustomValue( 'custom1' ) );
		$this->assertSame( '123', $requestInfo->getCustomValue( 'custom2' ) );
		$this->assertSame( '12.3', $requestInfo->getCustomValue( 'custom3' ) );
		$this->assertSame( '', $requestInfo->getCustomValue( 'custom4' ) );
		$this->assertSame( '1', $requestInfo->getCustomValue( 'custom5' ) );
		$this->assertSame( '', $requestInfo->getCustomValue( 'custom6' ) );
		$this->assertSame( '', $requestInfo->getCustomValue( 'not-existing' ) );
	}
}
