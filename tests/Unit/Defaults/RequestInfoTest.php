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

class RequestInfoTest extends \PHPUnit\Framework\TestCase
{
	public function testCanInstantiateFromEnvUsingGlobalServerVariable()
	{
		$requestTime      = microtime();
		$requestTimeFloat = microtime( true );

		$_SERVER['argv'] = [ 'unit', 'test' ];
		$_SERVER['argc']                 = 2;
		$_SERVER['REQUEST_URI']          = '/unit/test';
		$_SERVER['REQUEST_METHOD']       = 'POST';
		$_SERVER['QUERY_STRING']         = 'unit=test';
		$_SERVER['HTTP_USER_AGENT']      = 'phpunit cli';
		$_SERVER['HTTP_HOST']            = 'www.icehawk.de';
		$_SERVER['HTTP_CONNECTION']      = 'Keep Alive';
		$_SERVER['HTTP_REFERER']         = 'http://www.example.com';
		$_SERVER['HTTP_ACCEPT']          = 'text/html; charset=utf-8';
		$_SERVER['HTTP_ACCEPT_CHARSET']  = 'utf-8';
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
		$_SERVER['SERVER_ADDR']          = '127.0.0.1';
		$_SERVER['REMOTE_ADDR']          = '192.168.0.10';
		$_SERVER['REMOTE_HOST']          = 'example.com';
		$_SERVER['REMOTE_PORT']          = '5432';
		$_SERVER['REMOTE_USER']          = 'hollodotme';
		$_SERVER['REDIRECT_REMOTE_USER'] = 'hollodotme-redirect';
		$_SERVER['HTTP_REFERER']         = 'http://www.example.com';
		$_SERVER['REQUEST_TIME']         = $requestTime;
		$_SERVER['REQUEST_TIME_FLOAT']   = $requestTimeFloat;
		$_SERVER['HTTPS']                = 'on';
		$_SERVER['AUTH_TYPE']            = 'Basic';
		$_SERVER['PHP_AUTH_DIGEST']      = '@somedigest.string';
		$_SERVER['PHP_AUTH_USER']        = 'Unit';
		$_SERVER['PHP_AUTH_PW']          = 'Test';
		$_SERVER['CONTENT_LENGTH']       = '512';
		$_SERVER['CONTENT_TYPE']         = 'application/json';
		$_SERVER['PHP_SELF']             = 'relative/path/to/script.php';
		$_SERVER['GATEWAY_INTERFACE']    = 'CGI/1.1';
		$_SERVER['SERVER_NAME']          = 'www.example.com';
		$_SERVER['SERVER_SOFTWARE']      = 'nginx 1.9';
		$_SERVER['SERVER_PROTOCOL']      = 'HTTP/1.1';
		$_SERVER['SERVER_ADMIN']         = 'admin@example.com';
		$_SERVER['SERVER_PORT']          = '8080';
		$_SERVER['SERVER_SIGNATURE']     = 'Ubuntu 14.04 Trusty - example.com';
		$_SERVER['PATH_TRANSLATED']      = '/real/path/to/docs';
		$_SERVER['DOCUMENT_ROOT']        = '/path/to/docs';
		$_SERVER['SCRIPT_NAME']          = 'path/to/script.php';
		$_SERVER['SCRIPT_FILENAME']      = '/absolute/path/to/script.php';
		$_SERVER['PATH_INFO']            = '/some/path';
		$_SERVER['ORIG_PATH_INFO']       = '/original/path';

		$requestInfo = RequestInfo::fromEnv();

		$this->assertInstanceOf( RequestInfo::class, $requestInfo );

		$this->assertSame( [ 'unit', 'test' ], $requestInfo->getArgv() );
		$this->assertSame( 2, $requestInfo->getArgc() );
		$this->assertSame( '/unit/test', $requestInfo->getUri() );
		$this->assertSame( 'POST', $requestInfo->getMethod() );
		$this->assertSame( 'unit=test', $requestInfo->getQueryString() );
		$this->assertSame( 'phpunit cli', $requestInfo->getUserAgent() );
		$this->assertSame( 'www.icehawk.de', $requestInfo->getHost() );
		$this->assertSame( 'Keep Alive', $requestInfo->getConnection() );
		$this->assertSame( 'http://www.example.com', $requestInfo->getReferer() );
		$this->assertSame( 'text/html; charset=utf-8', $requestInfo->getAcceptedContentTypes() );
		$this->assertSame( 'utf-8', $requestInfo->getAcceptedCharsets() );
		$this->assertSame( 'gzip', $requestInfo->getAcceptedEncoding() );
		$this->assertSame( 'en', $requestInfo->getAcceptedLanguage() );
		$this->assertSame( '127.0.0.1', $requestInfo->getServerAddress() );
		$this->assertSame( '192.168.0.10', $requestInfo->getClientAddress() );
		$this->assertSame( 'example.com', $requestInfo->getClientHost() );
		$this->assertSame( '5432', $requestInfo->getClientPort() );
		$this->assertSame( 'hollodotme', $requestInfo->getClientUser() );
		$this->assertSame( 'hollodotme-redirect', $requestInfo->getRedirectClientUser() );
		$this->assertSame( 'http://www.example.com', $requestInfo->getReferer() );
		$this->assertSame( $requestTime, $requestInfo->getRequestTime() );
		$this->assertSame( 'Basic', $requestInfo->getAuthType() );
		$this->assertSame( '@somedigest.string', $requestInfo->getAuthDigest() );
		$this->assertSame( 'Unit', $requestInfo->getAuthUser() );
		$this->assertSame( 'Test', $requestInfo->getAuthPassword() );
		$this->assertSame( $requestTimeFloat, $requestInfo->getRequestTimeFloat() );
		$this->assertSame( '512', $requestInfo->getContentLength() );
		$this->assertSame( 'application/json', $requestInfo->getContentType() );
		$this->assertSame( 'relative/path/to/script.php', $requestInfo->getPhpSelf() );
		$this->assertSame( 'CGI/1.1', $requestInfo->getGatewayInterface() );
		$this->assertSame( 'www.example.com', $requestInfo->getServerName() );
		$this->assertSame( 'nginx 1.9', $requestInfo->getServerSoftware() );
		$this->assertSame( 'HTTP/1.1', $requestInfo->getServerProtocol() );
		$this->assertSame( 'admin@example.com', $requestInfo->getServerAdmin() );
		$this->assertSame( '8080', $requestInfo->getServerPort() );
		$this->assertSame( 'Ubuntu 14.04 Trusty - example.com', $requestInfo->getServerSignature() );
		$this->assertSame( '/real/path/to/docs', $requestInfo->getPathTranslated() );
		$this->assertSame( '/path/to/docs', $requestInfo->getDocumentRoot() );
		$this->assertSame( 'path/to/script.php', $requestInfo->getScriptName() );
		$this->assertSame( '/absolute/path/to/script.php', $requestInfo->getScriptFilename() );
		$this->assertSame( '/some/path', $requestInfo->getPathInfo() );
		$this->assertSame( '/original/path', $requestInfo->getOriginalPathInfo() );
		$this->assertTrue( $requestInfo->isSecure() );
	}

	public function testGettersReturnEmptyValuesIfKeyIsNotSetInServerData()
	{
		$requestInfo = new RequestInfo( [] );

		$this->assertSame( [], $requestInfo->getArgv() );
		$this->assertSame( 0, $requestInfo->getArgc() );
		$this->assertSame( '/', $requestInfo->getUri() );
		$this->assertSame( '', $requestInfo->getMethod() );
		$this->assertSame( '', $requestInfo->getQueryString() );
		$this->assertSame( '', $requestInfo->getUserAgent() );
		$this->assertSame( '', $requestInfo->getHost() );
		$this->assertSame( '', $requestInfo->getConnection() );
		$this->assertSame( '', $requestInfo->getReferer() );
		$this->assertSame( '', $requestInfo->getAcceptedContentTypes() );
		$this->assertSame( '', $requestInfo->getAcceptedCharsets() );
		$this->assertSame( '', $requestInfo->getAcceptedEncoding() );
		$this->assertSame( '', $requestInfo->getAcceptedLanguage() );
		$this->assertSame( '', $requestInfo->getServerAddress() );
		$this->assertSame( '', $requestInfo->getClientAddress() );
		$this->assertSame( '', $requestInfo->getClientHost() );
		$this->assertSame( '', $requestInfo->getClientPort() );
		$this->assertSame( '', $requestInfo->getClientUser() );
		$this->assertSame( '', $requestInfo->getRedirectClientUser() );
		$this->assertSame( '', $requestInfo->getReferer() );
		$this->assertSame( '', $requestInfo->getRequestTime() );
		$this->assertSame( '', $requestInfo->getAuthType() );
		$this->assertSame( '', $requestInfo->getAuthDigest() );
		$this->assertSame( '', $requestInfo->getAuthUser() );
		$this->assertSame( '', $requestInfo->getAuthPassword() );
		$this->assertSame( 0.0, $requestInfo->getRequestTimeFloat() );
		$this->assertSame( '', $requestInfo->getContentLength() );
		$this->assertSame( '', $requestInfo->getContentType() );
		$this->assertSame( '', $requestInfo->getPhpSelf() );
		$this->assertSame( '', $requestInfo->getGatewayInterface() );
		$this->assertSame( '', $requestInfo->getServerName() );
		$this->assertSame( '', $requestInfo->getServerSoftware() );
		$this->assertSame( '', $requestInfo->getServerProtocol() );
		$this->assertSame( '', $requestInfo->getServerAdmin() );
		$this->assertSame( '', $requestInfo->getServerPort() );
		$this->assertSame( '', $requestInfo->getServerSignature() );
		$this->assertSame( '', $requestInfo->getPathTranslated() );
		$this->assertSame( '', $requestInfo->getDocumentRoot() );
		$this->assertSame( '', $requestInfo->getScriptName() );
		$this->assertSame( '', $requestInfo->getScriptFilename() );
		$this->assertSame( '', $requestInfo->getPathInfo() );
		$this->assertSame( '', $requestInfo->getOriginalPathInfo() );
		$this->assertFalse( $requestInfo->isSecure() );
	}

	public function testGetUriReturnSlashIfKeyIsNotSetInServerData()
	{
		$requestInfo = new RequestInfo( [] );

		$this->assertEquals( '/', $requestInfo->getUri() );
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
			[ 'head', 'HEAD' ],
			[ 'HeaD', 'HEAD' ],
			[ 'post', 'POST' ],
			[ 'pOSt', 'POST' ],
		];
	}

	public function testHttpsIsCheckedCaseInsensitive()
	{
		$requestInfo1 = new RequestInfo( [ 'HTTPS' => 'On' ] );
		$requestInfo2 = new RequestInfo( [ 'HTTPS' => 'on' ] );
		$requestInfo3 = new RequestInfo( [ 'HTTPS' => 'oN' ] );
		$requestInfo4 = new RequestInfo( [ 'HTTPS' => 'ON' ] );

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
