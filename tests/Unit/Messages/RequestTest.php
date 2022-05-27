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
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

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
	 * @throws InvalidArgumentException
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

		self::assertEquals( $expectedParameters, $request->getParsedBody() );
		self::assertEquals( $expectedParameters, $request->getCookieParams() );
		self::assertCount( 1, $request->getUploadedFiles() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsServerProtocol() : void
	{
		$_SERVER['SERVER_PROTOCOL'] = 'test';

		$request = Request::fromGlobals();

		self::assertEquals( 'test', $request->getProtocolVersion() );

		unset( $_SERVER['SERVER_PROTOCOL'] );

		$request = Request::fromGlobals();

		self::assertEquals( 'HTTP/1.1', $request->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithProtocolVersion() : void
	{
		$protocolVersion = 'HTTP/1.2';
		$request         = Request::fromGlobals()->withProtocolVersion( $protocolVersion );

		self::assertEquals( $protocolVersion, $request->getProtocolVersion() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testHeaderLine() : void
	{
		$headerValueArray              = 'Basic foo, Bearer bar';
		$_SERVER['HTTP_AUTHORIZATION'] = $headerValueArray;
		$request                       = Request::fromGlobals();

		self::assertEquals( $headerValueArray, $request->getHeaderLine( self::VALID_HEADER_NAME ) );
		self::assertEmpty( $request->getHeaderLine( 'foo' ) );
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

		$request = Request::fromGlobals()->withHeader( self::VALID_HEADER_NAME, $headerValue );
		$headers = $request->getHeaders();
		self::assertIsArray( $headers );
		self::assertCount( 1, $headers );

		$header = $request->getHeader( self::VALID_HEADER_NAME );
		self::assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAddedHeaderCreatesHeaderIfNotExisting() : void
	{
		$request = Request::fromGlobals();

		self::assertFalse( $request->hasHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Unit' );

		$expectedHeaders = ['X-Test' => ['Unit']];

		self::assertSame( $expectedHeaders, $newRequest->getHeaders() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAddedHeaderAddsHeaderValuesToExistingHeader() : void
	{
		$_SERVER['HTTP_X_TEST'] = 'First Unit';
		$request                = Request::fromGlobals();

		self::assertTrue( $request->hasHeader( 'X-Test' ) );
		self::assertSame( ['First Unit'], $request->getHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Second Unit' );

		$expectedHeaders = ['X-Test' => ['First Unit', 'Second Unit']];

		self::assertSame( $expectedHeaders, $newRequest->getHeaders() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAddedHeaderKeepsHeaderValuesUnique() : void
	{
		$_SERVER['HTTP_X_TEST'] = 'First Unit';
		$request                = Request::fromGlobals();

		self::assertTrue( $request->hasHeader( 'X-Test' ) );
		self::assertSame( ['First Unit'], $request->getHeader( 'X-Test' ) );

		$newRequest = $request->withAddedHeader( 'X-Test', 'Second Unit' );

		$expectedHeaders = ['X-Test' => ['First Unit', 'Second Unit']];

		self::assertSame( $expectedHeaders, $newRequest->getHeaders() );

		$newerRequest = $newRequest->withAddedHeader( 'X-Test', 'Second Unit' );

		self::assertSame( $expectedHeaders, $newerRequest->getHeaders() );
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

		$request        = Request::fromGlobals();
		$clonedInstance = $request->withoutHeader( 'User-Agent' );
		$headers        = $clonedInstance->getHeaders();

		self::assertCount( 1, $headers );
		self::assertFalse( $clonedInstance->hasHeader( 'User-Agent' ) );

		$headers = $request->withoutHeader( 'foo' )->getHeaders();

		self::assertIsArray( $headers );
		self::assertCount( 2, $headers );

		$header = $request->getHeader( self::VALID_HEADER_NAME );
		self::assertEquals( $headerValue, array_shift( $header ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsBody() : void
	{
		$request = Request::fromGlobals();
		$stream  = Stream::memory();
		$stream->write( 'Unit-Test' );

		$anotherServerRequest = $request->withBody( $stream );
		self::assertSame( (string)$anotherServerRequest->getBody(), (string)$stream );
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
		$request                 = Request::fromGlobals();

		self::assertEquals( $expected, $request->getRequestTarget() );
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
	 * @throws InvalidArgumentException
	 */
	public function testWithRequestTarget() : void
	{
		$expectedServerParameters = [
			'REQUEST_URI'  => '/api/v1/streams/mega-stream/events',
			'QUERY_STRING' => 'eventName=OrderPlaced&limit=2',
		];

		$uri     = 'https://api.localhost/api/v1/streams/mega-stream/events?eventName=OrderPlaced&limit=2';
		$request = Request::fromGlobals()->withRequestTarget( $uri );

		self::assertEquals(
			$expectedServerParameters['REQUEST_URI'],
			$request->getServerParams()['REQUEST_URI']
		);
		self::assertEquals(
			$expectedServerParameters['QUERY_STRING'],
			$request->getServerParams()['QUERY_STRING']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsRequestMethod() : void
	{
		$request = Request::fromGlobals();
		self::assertEquals( 'UNKNOWN', $request->getMethod() );

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$request                   = Request::fromGlobals();
		self::assertEquals( 'GET', $request->getMethod() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithMethod() : void
	{
		$requestMethod = 'GET';
		$request       = Request::fromGlobals()->withMethod( $requestMethod );

		self::assertEquals( $requestMethod, $request->getMethod() );
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

		self::assertInstanceOf( Uri::class, $uri );
		self::assertEquals( $expected, (string)$uri );
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
	 * @param string          $scheme
	 * @param string          $authUser
	 * @param string          $authPassword
	 * @param string          $host
	 * @param null|string|int $port
	 * @param string          $requestUri
	 * @param string          $queryString
	 * @param string          $fragment
	 * @param string          $expected
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithUri(
		string $scheme,
		string $authUser,
		string $authPassword,
		string $host,
		null|string|int $port,
		string $requestUri,
		string $queryString,
		string $fragment,
		string $expected
	) : void
	{
		$uri = Uri::fromComponents(
			[
				'scheme'   => $scheme,
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

		self::assertSame( $expected, (string)$request->getUri() );
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
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsCookieParameters() : void
	{
		$_COOKIE['foo'] = 'bar';
		$request        = Request::fromGlobals();

		self::assertNotEmpty( $request->getCookieParams() );
		self::assertEquals( 'bar', $request->getCookieParams()['foo'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithCookieParameters() : void
	{
		$cookieParameters = ['unit' => 'test'];
		$request          = Request::fromGlobals()->withCookieParams( $cookieParameters );

		self::assertEquals( $cookieParameters, $request->getCookieParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsQueryParameters() : void
	{
		$_GET['foo'] = 'bar';

		self::assertEquals( ['foo' => 'bar'], Request::fromGlobals()->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithQueryParameters() : void
	{
		$queryParameters = ['foo' => 'bar'];
		$request         = Request::fromGlobals()->withQueryParams( $queryParameters );

		self::assertEquals( $queryParameters, $request->getQueryParams() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsUploadedFiles() : void
	{
		$fileKey            = 'foo';
		$_FILES[ $fileKey ] = ['name' => 'unit'];

		$request = Request::fromGlobals();

		self::assertNotEmpty( $request->getUploadedFiles() );
		self::assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$request->getUploadedFiles()[ $fileKey ]
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function testGetUploadedFilesByName() : void
	{
		$request = Request::fromGlobals()->withUploadedFiles( $this->uploadedFilesArray() );

		self::assertNotEmpty( $request->getUploadedFiles() );
		self::assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$request->getUploadedFilesByName( 'test' )
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function testGetUploadedFileByNameAndIndex() : void
	{
		$request = Request::fromGlobals()->withUploadedFiles( $this->uploadedFilesArray() );

		self::assertNotEmpty( $request->getUploadedFiles() );
		self::assertSame(
			UPLOAD_ERR_OK,
			$request->getUploadedFile( 'test' )->getError()
		);
		self::assertSame(
			UPLOAD_ERR_NO_FILE,
			$request->getUploadedFile( 'test', 1 )->getError()
		);
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithUploadedFiles() : void
	{
		$request = Request::fromGlobals()->withUploadedFiles( $this->uploadedFilesArray() );

		self::assertCount( 2, $request->getUploadedFiles()['test'] );
		self::assertContainsOnlyInstancesOf(
			UploadedFile::class,
			$request->getUploadedFiles()['test']
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsParsedBody() : void
	{
		$_POST['foo'] = 'bar';

		self::assertEquals( ['foo' => 'bar'], Request::fromGlobals()->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithParsedBody() : void
	{
		$parsedBody = ['foo' => 'bar'];
		$request    = Request::fromGlobals()->withParsedBody( $parsedBody );

		self::assertEquals( $parsedBody, $request->getParsedBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsAttributes() : void
	{
		$attributes = Request::fromGlobals()->getAttributes();

		self::assertIsArray( $attributes );
		self::assertEmpty( $attributes );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithAttribute() : void
	{
		$request = Request::fromGlobals()->withAttribute( 'foo', 'bar' );

		self::assertCount( 1, $request->getAttributes() );
		self::assertEquals( 'bar', $request->getAttribute( 'foo' ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithoutAttribute() : void
	{
		$request = Request::fromGlobals()->withAttribute( 'foo', 'bar' );

		self::assertCount( 1, $request->getAttributes() );
		self::assertEquals( 'bar', $request->getAttribute( 'foo' ) );

		$clonedInstance = $request->withoutAttribute( 'test' );
		self::assertCount( 1, $clonedInstance->getAttributes() );
		self::assertEquals( 'bar', $clonedInstance->getAttribute( 'foo' ) );

		$anotherClonedInstance = $clonedInstance->withoutAttribute( 'foo' );
		self::assertEmpty( $anotherClonedInstance->getAttributes() );
	}

	/**
	 * @dataProvider invalidInputStringDataProvider
	 *
	 * @param mixed $value
	 *
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
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
	 * @return Generator<array<int, mixed>>
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
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsInputString() : void
	{
		$_REQUEST['foo'] = 'bar';
		$request         = Request::fromGlobals();

		self::assertEquals( 'bar', $request->getInputString( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsInputStringIfDefaultParameterProvided() : void
	{
		$defaultValue = 'test';
		$request      = Request::fromGlobals();

		self::assertEquals( $defaultValue, $request->getInputString( 'unit', $defaultValue ) );
	}

	/**
	 * @dataProvider invalidInputArrayDataProvider
	 *
	 * @param mixed $value
	 *
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testGetInputArrayThrowsExceptionIfValueIsNotAnArray( mixed $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not an array' );

		/** @noinspection UnusedFunctionResultInspection */
		$request->getInputArray( 'foo' );
	}

	/**
	 * @return Generator<array<int, mixed>>
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
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsInputArray() : void
	{
		$value           = ['unit' => 'test'];
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		self::assertEquals( $value, $request->getInputArray( 'foo' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testItReturnsInputArrayIfDefaultParameterProvided() : void
	{
		$defaultValue = ['unit' => 'test'];
		$request      = Request::fromGlobals();

		self::assertEquals( $defaultValue, $request->getInputArray( 'unit', $defaultValue ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider invalidGetInputIntProvider
	 */
	public function testGetInputIntThrowsExceptionIfValueIsNotCastableToInt( mixed $value ) : void
	{
		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as integer' );

		$request->getInputInt( 'foo' );
	}

	/**
	 * @return Generator<array<int, mixed>>
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
	 * @throws InvalidArgumentException
	 */
	public function testGetInputIntThrowsExceptionIfKeyIsNotSetAndDefaultValueIsNull() : void
	{
		unset( $_REQUEST['foo'] );

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as integer' );

		Request::fromGlobals()->getInputInt( 'foo' );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function testGetInputIntReturnsDefaultValueIfKeyIsNotSet() : void
	{
		unset( $_REQUEST['foo'] );

		self::assertSame( 123, Request::fromGlobals()->getInputInt( 'foo', 123 ) );
	}

	/**
	 * @param string $value
	 * @param float  $expectedFloat
	 *
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 * @dataProvider validFloatInputProvider
	 */
	public function testGetInputFloat( string $value, float $expectedFloat ) : void
	{
		$_REQUEST['foo'] = $value;

		self::assertSame( $expectedFloat, Request::fromGlobals()->getInputFloat( 'foo' ) );
	}

	/**
	 * @return array<array<string, mixed>>
	 */
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
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider invalidGetInputFloatProvider
	 */
	public function testGetInputFloatThrowsExceptionIfValueIsNotCastableToFloat( mixed $value ) : void
	{
		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as float' );

		$_REQUEST['foo'] = $value;
		$request         = Request::fromGlobals();

		/** @noinspection UnusedFunctionResultInspection */
		$request->getInputFloat( 'foo' );
	}

	/**
	 * @return Generator<array<int, mixed>>
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
	 * @throws InvalidArgumentException
	 */
	public function testGetInputFloatThrowsExceptionIfKeyIsNotSetAndDefaultValueIsNull() : void
	{
		unset( $_REQUEST['foo'] );

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( 'Input for key "foo" is not castable as float' );

		/** @noinspection UnusedFunctionResultInspection */
		Request::fromGlobals()->getInputFloat( 'foo' );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws UnexpectedValueException
	 * @throws InvalidArgumentException
	 */
	public function testGetInputFloatReturnsDefaultValueIfKeyIsNotSet() : void
	{
		unset( $_REQUEST['foo'] );

		self::assertSame( 12.3, Request::fromGlobals()->getInputFloat( 'foo', 12.3 ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testHasInputKey() : void
	{
		$_REQUEST['foo'] = null;
		$_REQUEST['bar'] = 'string';
		$_REQUEST['baz'] = ['array'];

		$request = Request::fromGlobals();

		self::assertTrue( $request->hasInputKey( 'foo' ) );
		self::assertTrue( $request->hasInputKey( 'bar' ) );
		self::assertTrue( $request->hasInputKey( 'baz' ) );

		self::assertFalse( $request->hasInputKey( 'foo-bar-baz' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testIsInputNull() : void
	{
		$_REQUEST['foo'] = null;

		$request = Request::fromGlobals();

		self::assertTrue( $request->isInputNull( 'foo' ) );
		self::assertFalse( $request->isInputNull( 'bar' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testForwardedProtocolPrecedesHttpsFlag() : void
	{
		$_SERVER['HTTPS'] = null;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';

		$expectedUri = 'http://example.com';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );

		$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
		$expectedUri                       = 'https://example.com';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testForwardedPortPrecedesServerPort() : void
	{
		$_SERVER['SERVER_PORT'] = 8080;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';

		$expectedUri = 'http://example.com:8080';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );

		$_SERVER['HTTP_X_FORWARDED_PORT'] = 9090;

		$expectedUri = 'http://example.com:9090';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testForwardedHostPrecedesHttpHost() : void
	{
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';

		$expectedUri = 'http://example.com';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );

		$_SERVER['HTTP_X_FORWARDED_HOST'] = 'unit-test.de';

		$expectedUri = 'http://unit-test.de';

		self::assertEquals( $expectedUri, (string)Request::fromGlobals()->getUri() );
	}
}
