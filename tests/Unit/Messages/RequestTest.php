<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use Generator;
use IceHawk\IceHawk\Messages\Request;
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
use UnexpectedValueException;
use const PHP_FLOAT_MAX;
use const PHP_FLOAT_MIN;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class RequestTest extends TestCase
{
	use UploadedFilesProviding;

	private const VALID_HEADER_NAME = 'Authorization';

	public function setUp() : void
	{
		$_SERVER['REQUEST_TIME']       = microtime();
		$_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
	}

	/**
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

		$request            = Request::fromGlobals();
		$expectedParameters = ['foo' => 'bar'];

		$this->assertEquals( $expectedParameters, $request->getParsedBody() );
		$this->assertEquals( $expectedParameters, $request->getCookieParams() );
		$this->assertCount( 1, $request->getUploadedFiles() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsServerProtocol() : void
	{
		$_SERVER['SERVER_PROTOCOL'] = 'test';

		$request = Request::fromGlobals();

		$this->assertEquals( 'test', $request->getProtocolVersion() );

		unset( $_SERVER['SERVER_PROTOCOL'] );

		$request = Request::fromGlobals();

		$this->assertEquals( 'HTTP/1.1', $request->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithProtocolVersion() : void
	{
		$protocolVersion = 'HTTP/1.2';
		$request         = Request::fromGlobals()->withProtocolVersion( $protocolVersion );

		$this->assertEquals( $protocolVersion, $request->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testHeaderLine() : void
	{
		$headerValueArray              = 'Basic foo, Bearer bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValueArray;
		$request                       = Request::fromGlobals();

		$this->assertEquals( $headerValueArray, $request->getHeaderLine( self::VALID_HEADER_NAME ) );
		$this->assertEmpty( $request->getHeaderLine( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testWithHeader() : void
	{
		$headerValue                   = 'Basic test';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

		$request = Request::fromGlobals()->withHeader( self::VALID_HEADER_NAME, $headerValue );
		$headers = $request->getHeaders();
		$this->assertIsArray( $headers );
		$this->assertCount( 1, $headers );

		$header = $request->getHeader( self::VALID_HEADER_NAME );
		$this->assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithAddedHeaderCreatesHeaderIfNotExisting() : void
	{
		$request = Request::fromGlobals();

		$this->assertFalse( $request->hasHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Unit' );

		$expectedHeaders = ['X-Test' => ['Unit']];

		$this->assertSame( $expectedHeaders, $newRequest->getHeaders() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithAddedHeaderAddsHeaderValuesToExistingHeader() : void
	{
		$_SERVER['HTTP_X_TEST'] = 'First Unit';
		$request                = Request::fromGlobals();

		$this->assertTrue( $request->hasHeader( 'X-Test' ) );
		$this->assertSame( ['First Unit'], $request->getHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Second Unit' );

		$expectedHeaders = ['X-Test' => ['First Unit', 'Second Unit']];

		$this->assertSame( $expectedHeaders, $newRequest->getHeaders() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithAddedHeaderKeepsHeaderValuesUnique() : void
	{
		$_SERVER['HTTP_X_TEST'] = 'First Unit';
		$request                = Request::fromGlobals();

		$this->assertTrue( $request->hasHeader( 'X-Test' ) );
		$this->assertSame( ['First Unit'], $request->getHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Second Unit' );

		$expectedHeaders = ['X-Test' => ['First Unit', 'Second Unit']];

		$this->assertSame( $expectedHeaders, $newRequest->getHeaders() );

		$newerRequest = $newRequest->withAddedHeader( 'X-Test', 'Second Unit' );

		$this->assertSame( $expectedHeaders, $newerRequest->getHeaders() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testWithoutHeader() : void
	{
		$headerValue                   = 'Basic test';
		$_SERVER['HTTP_USER_AGENT']    = 'bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

		$request        = Request::fromGlobals();
		$clonedInstance = $request->withoutHeader( 'User-Agent' );
		$headers        = $clonedInstance->getHeaders();

		$this->assertCount( 1, $headers );
		$this->assertFalse( $clonedInstance->hasHeader( 'User-Agent' ) );

		$headers = $request->withoutHeader( 'foo' )->getHeaders();

		$this->assertIsArray( $headers );
		$this->assertCount( 2, $headers );

		$header = $request->getHeader( self::VALID_HEADER_NAME );
		$this->assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testItReturnsBody() : void
	{
		$request = Request::fromGlobals();
		$stream  = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		$anotherServerRequest = $request->withBody( $stream );
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
	 */
	public function testRequestTarget( $uri, $queryString, string $expected ) : void
	{
		$_SERVER['REQUEST_URI']  = $uri;
		$_SERVER['QUERY_STRING'] = $queryString;
		$request                 = Request::fromGlobals();

		$this->assertEquals( $expected, $request->getRequestTarget() );
	}

	/**
	 * @return Generator<array<null|string>>
	 */
	public function requestTargetDataProvider() : Generator
	{
		yield [null, null, DIRECTORY_SEPARATOR];
		yield ['test', null, 'test'];
		yield ['test', 'eventName=OrderPlaced&limit=2', 'test?eventName=OrderPlaced&limit=2'];
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithRequestTarget() : void
	{
		$expectedServerParameters = [
			'REQUEST_URI'  => '/api/v1/streams/mega-stream/events',
			'QUERY_STRING' => 'eventName=OrderPlaced&limit=2',
		];

		$uri     = 'https://api.localhost/api/v1/streams/mega-stream/events?eventName=OrderPlaced&limit=2';
		$request = Request::fromGlobals()->withRequestTarget( $uri );

		$this->assertEquals(
			$expectedServerParameters['REQUEST_URI'],
			$request->getServerParams()['REQUEST_URI']
		);
		$this->assertEquals(
			$expectedServerParameters['QUERY_STRING'],
			$request->getServerParams()['QUERY_STRING']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsRequestMethod() : void
	{
		$request = Request::fromGlobals();
		$this->assertEquals( 'UNKNOWN', $request->getMethod() );

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$request                   = Request::fromGlobals();
		$this->assertEquals( 'GET', $request->getMethod() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithMethod() : void
	{
		$requestMethod = 'GET';
		$request       = Request::fromGlobals()->withMethod( $requestMethod );

		$this->assertEquals( $requestMethod, $request->getMethod() );
	}

	/**
	 * @dataProvider getUriDataProvider
	 *
	 * @param string   $https
	 * @param string   $authUser
	 * @param string   $authPassword
	 * @param string   $host
	 * @param int|null $port
	 * @param string   $requestUri
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
		string $requestUri,
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
		$_SERVER['REQUEST_URI']  = $requestUri;
		$_SERVER['QUERY_STRING'] = $queryString;
		$_SERVER['FRAGMENT']     = $fragment;

		$uri = Request::fromGlobals()->getUri();

		$this->assertInstanceOf( Uri::class, $uri );
		$this->assertEquals( $expected, (string)$uri );
	}

	/**
	 * @return Generator<array<null|string|int>>
	 */
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
			null,
			'/some/path?key=should-be-removed',
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
	 * @param string $requestUri
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
		string $requestUri,
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
				'path'     => $requestUri,
				'query'    => $queryString,
				'fragment' => $fragment,
			]
		);

		$request = Request::fromGlobals()->withUri( $uri, true );

		$this->assertSame( $expected, (string)$request->getUri() );
	}

	/**
	 * @return Generator<array<null|string|int>>
	 */
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
	 */
	public function testItReturnsCookieParameters() : void
	{
		$_COOKIE['foo'] = 'bar';
		$request        = Request::fromGlobals();

		$this->assertNotEmpty( $request->getCookieParams() );
		$this->assertEquals( 'bar', $request->getCookieParams()['foo'] );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithCookieParameters() : void
	{
		$cookieParameters = ['unit' => 'test'];
		$request          = Request::fromGlobals()->withCookieParams( $cookieParameters );

		$this->assertEquals( $cookieParameters, $request->getCookieParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsQueryParameters() : void
	{
		$_GET['foo'] = 'bar';

		$this->assertEquals( ['foo' => 'bar'], Request::fromGlobals()->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithQueryParameters() : void
	{
		$queryParameters = ['foo' => 'bar'];
		$request         = Request::fromGlobals()->withQueryParams( $queryParameters );

		$this->assertEquals( $queryParameters, $request->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsUploadedFiles() : void
	{
		$fileKey            = 'foo';
		$_FILES[ $fileKey ] = ['name' => 'unit'];

		$request = Request::fromGlobals();

		$this->assertNotEmpty( $request->getUploadedFiles() );
		$this->assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$request->getUploadedFiles()[ $fileKey ]
		);
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testWithUploadedFiles() : void
	{
		$request = Request::fromGlobals()->withUploadedFiles( $this->uploadedFilesArray() );

		$this->assertCount( 2, $request->getUploadedFiles()['test'] );
		$this->assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$request->getUploadedFiles()['test']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsParsedBody() : void
	{
		$_POST['foo'] = 'bar';

		$this->assertEquals( ['foo' => 'bar'], Request::fromGlobals()->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithParsedBody() : void
	{
		$parsedBody = ['foo' => 'bar'];
		$request    = Request::fromGlobals()->withParsedBody( $parsedBody );

		$this->assertEquals( $parsedBody, $request->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testItReturnsAttributes() : void
	{
		$attributes = Request::fromGlobals()->getAttributes();

		$this->assertIsArray( $attributes );
		$this->assertEmpty( $attributes );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testWithAttribute() : void
	{
		$request = Request::fromGlobals()->withAttribute( 'foo', 'bar' );

		$this->assertCount( 1, $request->getAttributes() );
		$this->assertEquals( 'bar', $request->getAttribute( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testWithoutAttribute() : void
	{
		$request = Request::fromGlobals()->withAttribute( 'foo', 'bar' );

		$this->assertCount( 1, $request->getAttributes() );
		$this->assertEquals( 'bar', $request->getAttribute( 'foo' ) );

		$clonedInstance = $request->withoutAttribute( 'test' );
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
	 * @throws RuntimeException
	 */
	public function testGetInputStringThrowsExceptionIfValueIsNotAString( $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not a string' );

		/** @noinspection UnusedFunctionResultInspection */
		$request->getInputString( 'foo' );
	}

	/**
	 * @return Generator<array<mixed>>
	 */
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
	 * @throws RuntimeException
	 */
	public function testItReturnsInputString() : void
	{
		$_REQUEST['foo'] = 'bar';
		$request         = Request::fromGlobals();

		$this->assertEquals( 'bar', $request->getInputString( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputStringIfDefaultParameterProvided() : void
	{
		$defaultValue = 'test';
		$request      = Request::fromGlobals();

		$this->assertEquals( $defaultValue, $request->getInputString( 'unit', $defaultValue ) );
	}

	/**
	 * @dataProvider invalidInputArrayDataProvider
	 *
	 * @param mixed $value
	 *
	 * @throws RuntimeException
	 */
	public function testGetInputArrayThrowsExceptionIfValueIsNotAnArray( $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not an array' );

		/** @noinspection UnusedFunctionResultInspection */
		$request->getInputArray( 'foo' );
	}

	/**
	 * @return Generator<array<mixed>>
	 */
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
	 * @throws RuntimeException
	 */
	public function testItReturnsInputArray() : void
	{
		$value           = ['unit' => 'test'];
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->assertEquals( $value, $request->getInputArray( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testItReturnsInputArrayIfDefaultParameterProvided() : void
	{
		$defaultValue = ['unit' => 'test'];
		$request      = Request::fromGlobals();

		$this->assertEquals( $defaultValue, $request->getInputArray( 'unit', $defaultValue ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @throws UnexpectedValueException
	 *
	 * @dataProvider invalidGetInputIntProvider
	 */
	public function testGetInputIntThrowsExceptionIfValueIsNotCastableToInt( $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as integer' );

		$request->getInputInt( 'foo' );
	}

	/**
	 * @return Generator<array<mixed>>
	 */
	public function invalidGetInputIntProvider() : Generator
	{
		yield [null];
		yield [false];
		yield [true];
		yield [''];
		yield ['test'];
		yield [12.3];
		yield [PHP_INT_MAX . '1'];
		yield [PHP_INT_MIN . '1'];
	}

	/**
	 * @throws UnexpectedValueException
	 */
	public function testGetInputIntThrowsExceptionIfKeyIsNotSetAndDefaultValueIsNull() : void
	{
		unset( $_REQUEST['foo'] );

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as integer' );

		Request::fromGlobals()->getInputInt( 'foo', null );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 */
	public function testGetInputIntReturnsDefaultValueIfKeyIsNotSet() : void
	{
		unset( $_REQUEST['foo'] );

		$this->assertSame( 123, Request::fromGlobals()->getInputInt( 'foo', 123 ) );
	}

	/**
	 * @param string $value
	 * @param float  $expectedFloat
	 *
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @dataProvider validFloatInputProvider
	 */
	public function testGetInputFloat( string $value, float $expectedFloat ) : void
	{
		$_REQUEST['foo'] = $value;

		$this->assertSame( $expectedFloat, Request::fromGlobals()->getInputFloat( 'foo' ) );
	}

	public function validFloatInputProvider() : array
	{
		return [
			[
				'value'         => '45.00',
				'expectedFloat' => 45.00,
			],
		];
	}

	/**
	 * @param mixed $value
	 *
	 * @throws UnexpectedValueException
	 *
	 * @dataProvider invalidGetInputFloatProvider
	 */
	public function testGetInputFloatThrowsExceptionIfValueIsNotCastableToFloat( $value ) : void
	{
		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as float' );

		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		/** @noinspection UnusedFunctionResultInspection */
		$request->getInputFloat( 'foo' );
	}

	/**
	 * @return Generator<array<mixed>>
	 */
	public function invalidGetInputFloatProvider() : Generator
	{
		yield [null];
		yield [false];
		yield [true];
		yield [''];
		yield ['test'];
		yield [PHP_FLOAT_MAX . '1'];
		yield [PHP_FLOAT_MIN . '1'];
	}

	/**
	 * @throws UnexpectedValueException
	 */
	public function testGetInputFloatThrowsExceptionIfKeyIsNotSetAndDefaultValueIsNull() : void
	{
		unset( $_REQUEST['foo'] );

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as float' );

		/** @noinspection UnusedFunctionResultInspection */
		Request::fromGlobals()->getInputFloat( 'foo', null );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 */
	public function testGetInputFloatReturnsDefaultValueIfKeyIsNotSet() : void
	{
		unset( $_REQUEST['foo'] );

		$this->assertSame( 12.3, Request::fromGlobals()->getInputFloat( 'foo', 12.3 ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testHasInputKey() : void
	{
		$_REQUEST['foo'] = null;
		$_REQUEST['bar'] = 'string';
		$_REQUEST['baz'] = ['array'];

		$request = Request::fromGlobals();

		$this->assertTrue( $request->hasInputKey( 'foo' ) );
		$this->assertTrue( $request->hasInputKey( 'bar' ) );
		$this->assertTrue( $request->hasInputKey( 'baz' ) );

		$this->assertFalse( $request->hasInputKey( 'foo-bar-baz' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testIsInputNull() : void
	{
		$_REQUEST['foo'] = null;

		$request = Request::fromGlobals();

		$this->assertTrue( $request->isInputNull( 'foo' ) );
		$this->assertFalse( $request->isInputNull( 'bar' ) );
	}
}
