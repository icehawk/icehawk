<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use Generator;
use IceHawk\IceHawk\Messages\ServerRequest;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\UploadedFile;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Tests\Fixtures\Traits\UploadedFilesProviding;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class ServerRequestTest extends TestCase
{
	use UploadedFilesProviding;

	private const VALID_HEADER_NAME = 'Authorization';

	public function setUp() : void
	{
		$_SERVER['REQUEST_TIME']       = microtime();
		$_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromGlobals() : void
	{
		$_SERVER['SERVER_PROTOCOL'] = 'test';
		$_GET['foo']                = 'bar';
		$_POST['foo']               = 'bar';
		$_COOKIE['foo']             = 'bar';
		$_FILES['foo']              = ['name' => 'test'];

		$serverRequest      = ServerRequest::fromGlobals();
		$expectedParameters = ['foo' => 'bar'];

		$this->assertEquals( $expectedParameters, $serverRequest->getParsedBody() );
		$this->assertEquals( $expectedParameters, $serverRequest->getCookieParams() );
		$this->assertCount( 1, $serverRequest->getUploadedFiles() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsServerProtocol() : void
	{
		$_SERVER['SERVER_PROTOCOL'] = 'test';

		$serverRequest = ServerRequest::fromGlobals();

		$this->assertEquals( 'test', $serverRequest->getProtocolVersion() );

		unset( $_SERVER['SERVER_PROTOCOL'] );

		$serverRequest = ServerRequest::fromGlobals();

		$this->assertEquals( 'HTTP/1.1', $serverRequest->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithProtocolVersion() : void
	{
		$protocolVersion = 'HTTP/1.2';
		$serverRequest   = ServerRequest::fromGlobals()->withProtocolVersion( $protocolVersion );

		$this->assertEquals( $protocolVersion, $serverRequest->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testHeaderLine() : void
	{
		$headerValueArray              = 'Basic foo, Bearer bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValueArray;
		$serverRequest                 = ServerRequest::fromGlobals();

		$this->assertEquals( $headerValueArray, $serverRequest->getHeaderLine( self::VALID_HEADER_NAME ) );
		$this->assertEmpty( $serverRequest->getHeaderLine( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithHeader() : void
	{
		$headerValue                   = 'Basic test';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

		$serverRequest = ServerRequest::fromGlobals()->withHeader( self::VALID_HEADER_NAME, $headerValue );
		$headers       = $serverRequest->getHeaders();
		$this->assertIsArray( $headers );
		$this->assertCount( 1, $headers );

		$header = $serverRequest->getHeader( self::VALID_HEADER_NAME );
		$this->assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAddedHeader() : void
	{
		$expectedCountHeaders          = 2;
		$headerValue                   = 'Basic test';
		$headerValueArray              = 'Basic foo, Bearer bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

		$serverRequest         = ServerRequest::fromGlobals()->withHeader( self::VALID_HEADER_NAME, $headerValue );
		$anotherClonedInstance = $serverRequest->withAddedHeader( self::VALID_HEADER_NAME, $headerValueArray );

		$this->assertTrue( $anotherClonedInstance->hasHeader( self::VALID_HEADER_NAME ) );
		$this->assertCount( $expectedCountHeaders, $anotherClonedInstance->getHeaders()[ self::VALID_HEADER_NAME ] );

		$withAddedHeaderInstance = $anotherClonedInstance->withAddedHeader( 'foo', 'bar' );
		$this->assertCount( $expectedCountHeaders, $withAddedHeaderInstance->getHeaders()[ self::VALID_HEADER_NAME ] );
		$this->assertFalse( $withAddedHeaderInstance->hasHeader( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithoutHeader() : void
	{
		$headerValue                   = 'Basic test';
		$_SERVER['HTTP_USER_AGENT']    = 'bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

		$serverRequest  = ServerRequest::fromGlobals();
		$clonedInstance = $serverRequest->withoutHeader( 'User-Agent' );
		$headers        = $clonedInstance->getHeaders();

		$this->assertCount( 1, $headers );
		$this->assertFalse( $clonedInstance->hasHeader( 'User-Agent' ) );

		$headers = $serverRequest->withoutHeader( 'foo' )->getHeaders();

		$this->assertIsArray( $headers );
		$this->assertCount( 2, $headers );

		$header = $serverRequest->getHeader( self::VALID_HEADER_NAME );
		$this->assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testItReturnsBody() : void
	{
		$serverRequest = ServerRequest::fromGlobals();
		$stream        = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		$anotherServerRequest = $serverRequest->withBody( $stream );
		$this->assertSame( (string)$anotherServerRequest->getBody(), (string)$stream );
	}

	/**
	 * @dataProvider requestTargetDataProvider
	 *
	 * @param mixed  $uri
	 * @param mixed  $queryString
	 * @param string $expected
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testRequestTarget( $uri, $queryString, string $expected ) : void
	{
		$_SERVER['REQUEST_URI']  = $uri;
		$_SERVER['QUERY_STRING'] = $queryString;
		$serverRequest           = ServerRequest::fromGlobals();

		$this->assertEquals( $expected, $serverRequest->getRequestTarget() );
	}

	public function requestTargetDataProvider() : Generator
	{
		yield [null, null, DIRECTORY_SEPARATOR];
		yield ['test', null, 'test'];
		yield ['test', 'eventName=OrderPlaced&limit=2', 'test?eventName=OrderPlaced&limit=2'];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithRequestTarget() : void
	{
		$expectedServerParameters = [
			'REQUEST_URI'  => '/api/v1/streams/mega-stream/events',
			'QUERY_STRING' => 'eventName=OrderPlaced&limit=2',
		];

		$uri           = 'https://api.localhost/api/v1/streams/mega-stream/events?eventName=OrderPlaced&limit=2';
		$serverRequest = ServerRequest::fromGlobals()->withRequestTarget( $uri );

		$this->assertEquals(
			$expectedServerParameters['REQUEST_URI'],
			$serverRequest->getServerParams()['REQUEST_URI']
		);
		$this->assertEquals(
			$expectedServerParameters['QUERY_STRING'],
			$serverRequest->getServerParams()['QUERY_STRING']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsRequestMethod() : void
	{
		$serverRequest = ServerRequest::fromGlobals();
		$this->assertEquals( 'UNKNOWN', $serverRequest->getMethod() );

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$serverRequest             = ServerRequest::fromGlobals();
		$this->assertEquals( 'GET', $serverRequest->getMethod() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithMethod() : void
	{
		$requestMethod = 'GET';
		$serverRequest = ServerRequest::fromGlobals()->withMethod( $requestMethod );

		$this->assertEquals( $requestMethod, $serverRequest->getMethod() );
	}

	/**
	 * @dataProvider getUriDataProvider
	 *
	 * @param string   $https
	 * @param string   $authUser
	 * @param string   $authPassword
	 * @param string   $host
	 * @param int|null $port
	 * @param string   $pathInfo
	 * @param string   $queryString
	 * @param string   $fragment
	 * @param string   $expected
	 *
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetUri(
		string $https,
		string $authUser,
		string $authPassword,
		string $host,
		?int $port,
		string $pathInfo,
		string $queryString,
		string $fragment,
		string $expected
	) : void
	{
		$_SERVER['HTTPS']          = $https;
		$_SERVER['HTTP_AUTH_USER'] = $authUser;
		$_SERVER['HTTP_AUTH_PW']   = $authPassword;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = $host;
		$_SERVER['SERVER_PORT']  = $port;
		$_SERVER['PATH_INFO']    = $pathInfo;
		$_SERVER['QUERY_STRING'] = $queryString;
		$_SERVER['FRAGMENT']     = $fragment;

		$uri = ServerRequest::fromGlobals()->getUri();

		$this->assertInstanceOf( Uri::class, $uri );
		$this->assertEquals( $expected, (string)$uri );
	}

	public function getUriDataProvider() : Generator
	{
		yield ['', '', '', 'example.com', null, '', '', '', 'http://example.com'];
		yield ['', '', '', 'example.com', null, '', '', 'anchor', 'http://example.com#anchor'];
		yield ['', '', '', 'example.com', null, '', 'var=value', 'anchor', 'http://example.com?var=value#anchor'];
		yield [
			'',
			'',
			'',
			'example.com',
			null,
			'/some/path',
			'var=value',
			'anchor',
			'http://example.com/some/path?var=value#anchor',
		];
		yield [
			'',
			'',
			'',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://example.com:8080/some/path?var=value#anchor',
		];
		yield [
			'',
			'',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://:pass@example.com:8080/some/path?var=value#anchor',
		];
		yield [
			'',
			'user',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://user:pass@example.com:8080/some/path?var=value#anchor',
		];
		yield [
			'https',
			'user',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'https://user:pass@example.com:8080/some/path?var=value#anchor',
		];
	}

	/**
	 * @dataProvider withUriDataProvider
	 *
	 * @param string $https
	 * @param string $authUser
	 * @param string $authPassword
	 * @param string $host
	 * @param string $port
	 * @param string $pathInfo
	 * @param string $queryString
	 * @param string $fragment
	 * @param string $expected
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithUri(
		string $https,
		string $authUser,
		string $authPassword,
		string $host,
		$port,
		string $pathInfo,
		string $queryString,
		string $fragment,
		string $expected
	) : void
	{
		$uri = Uri::fromComponents(
			[
				'scheme'   => $https,
				'user'     => $authUser,
				'pass'     => $authPassword,
				'host'     => $host,
				'port'     => $port,
				'path'     => $pathInfo,
				'query'    => $queryString,
				'fragment' => $fragment,
			]
		);

		$serverRequest = ServerRequest::fromGlobals()->withUri( $uri, true );

		$this->assertEquals( $expected, (string)$serverRequest->getUri() );
	}

	public function withUriDataProvider() : Generator
	{
		yield ['', '', '', 'example.com', null, '', '', '', 'http://example.com'];
		yield ['', '', '', 'example.com', null, '', '', 'anchor', 'http://example.com#anchor'];
		yield ['', '', '', 'example.com', null, '', 'var=value', 'anchor', 'http://example.com?var=value#anchor'];
		yield [
			'',
			'',
			'',
			'example.com',
			null,
			'/some/path',
			'var=value',
			'anchor',
			'http://example.com/some/path?var=value#anchor',
		];
		yield [
			'',
			'',
			'',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://example.com/some/path?var=value#anchor',
		];
		yield [
			'',
			'',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://:pass@example.com/some/path?var=value#anchor',
		];
		yield [
			'',
			'user',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'http://user:pass@example.com/some/path?var=value#anchor',
		];
		yield [
			'https',
			'user',
			'pass',
			'example.com',
			8080,
			'/some/path',
			'var=value',
			'anchor',
			'https://user:pass@example.com/some/path?var=value#anchor',
		];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsCookieParameters() : void
	{
		$_COOKIE['foo'] = 'bar';
		$serverRequest  = ServerRequest::fromGlobals();

		$this->assertNotEmpty( $serverRequest->getCookieParams() );
		$this->assertEquals( 'bar', $serverRequest->getCookieParams()['foo'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithCookieParameters() : void
	{
		$cookieParameters = ['unit' => 'test'];
		$serverRequest    = ServerRequest::fromGlobals()->withCookieParams( $cookieParameters );

		$this->assertEquals( $cookieParameters, $serverRequest->getCookieParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsQueryParameters() : void
	{
		$_GET['foo'] = 'bar';

		$this->assertEquals( ['foo' => 'bar'], ServerRequest::fromGlobals()->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithQueryParameters() : void
	{
		$queryParameters = ['foo' => 'bar'];
		$serverRequest   = ServerRequest::fromGlobals()->withQueryParams( $queryParameters );

		$this->assertEquals( $queryParameters, $serverRequest->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsUploadedFiles() : void
	{
		$fileKey            = 'foo';
		$_FILES[ $fileKey ] = ['name' => 'unit'];

		$serverRequest = ServerRequest::fromGlobals();

		$this->assertNotEmpty( $serverRequest->getUploadedFiles() );
		$this->assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$serverRequest->getUploadedFiles()[ $fileKey ]
		);
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithUploadedFiles() : void
	{
		$serverRequest = ServerRequest::fromGlobals()->withUploadedFiles( $this->uploadedFilesArray() );

		$this->assertCount( 2, $serverRequest->getUploadedFiles()['test'] );
		$this->assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$serverRequest->getUploadedFiles()['test']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsParsedBody() : void
	{
		$_POST['foo'] = 'bar';

		$this->assertEquals( ['foo' => 'bar'], ServerRequest::fromGlobals()->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithParsedBody() : void
	{
		$parsedBody    = ['foo' => 'bar'];
		$serverRequest = ServerRequest::fromGlobals()->withParsedBody( $parsedBody );

		$this->assertEquals( $parsedBody, $serverRequest->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsAttributes() : void
	{
		$attributes = ServerRequest::fromGlobals()->getAttributes();

		$this->assertIsArray( $attributes );
		$this->assertEmpty( $attributes );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAttribute() : void
	{
		$serverRequest = ServerRequest::fromGlobals()->withAttribute( 'foo', 'bar' );

		$this->assertCount( 1, $serverRequest->getAttributes() );
		$this->assertEquals( 'bar', $serverRequest->getAttribute( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithoutAttribute() : void
	{
		$serverRequest = ServerRequest::fromGlobals()->withAttribute( 'foo', 'bar' );

		$this->assertCount( 1, $serverRequest->getAttributes() );
		$this->assertEquals( 'bar', $serverRequest->getAttribute( 'foo' ) );

		$clonedInstance = $serverRequest->withoutAttribute( 'test' );
		$this->assertCount( 1, $clonedInstance->getAttributes() );
		$this->assertEquals( 'bar', $clonedInstance->getAttribute( 'foo' ) );

		$anotherClonedInstance = $clonedInstance->withoutAttribute( 'foo' );
		$this->assertEmpty( $anotherClonedInstance->getAttributes() );
	}

	/**
	 * @dataProvider invalidInputStringDataProvider
	 *
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testGetInputStringThrowsExceptionIfValueIsNotAString( $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$serverRequest   = ServerRequest::fromGlobals();

		$this->expectException( RuntimeException::class );

		/** @noinspection UnusedFunctionResultInspection */
		$serverRequest->getInputString( 'foo' );
	}

	public function invalidInputStringDataProvider() : Generator
	{
		yield [['foo' => 'bar']];
		yield [null];
		yield [3534543];
		yield [100.];
		yield [new stdClass()];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputString() : void
	{
		$_REQUEST['foo'] = 'bar';
		$serverRequest   = ServerRequest::fromGlobals();

		$this->assertEquals( 'bar', $serverRequest->getInputString( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputStringIfDefaultParameterProvided() : void
	{
		$defaultValue  = 'test';
		$serverRequest = ServerRequest::fromGlobals();

		$this->assertEquals( $defaultValue, $serverRequest->getInputString( 'unit', $defaultValue ) );
	}

	/**
	 * @dataProvider invalidInputArrayDataProvider
	 *
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testGetInputArrayThrowsExceptionIfValueIsNotAString( $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$serverRequest   = ServerRequest::fromGlobals();

		$this->expectException( RuntimeException::class );

		/** @noinspection UnusedFunctionResultInspection */
		$serverRequest->getInputArray( 'foo' );
	}

	public function invalidInputArrayDataProvider() : Generator
	{
		yield ['test'];
		yield [''];
		yield [null];
		yield [3534543];
		yield [100.];
		yield [new stdClass()];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputArray() : void
	{
		$value           = ['unit' => 'test'];
		$_REQUEST['foo'] = $value;
		$serverRequest   = ServerRequest::fromGlobals();

		$this->assertEquals( $value, $serverRequest->getInputArray( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputArrayIfDefaultParameterProvided() : void
	{
		$defaultValue  = ['unit' => 'test'];
		$serverRequest = ServerRequest::fromGlobals();

		$this->assertEquals( $defaultValue, $serverRequest->getInputArray( 'unit', $defaultValue ) );
	}
}
