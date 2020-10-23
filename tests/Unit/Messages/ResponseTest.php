<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ResponseTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithHeader() : void
	{
		$response = Response::new()
		                    ->withHeader( 'X-Test', 'Unit-Test' )
		                    ->withHeader( 'X-Multiple', ['Unit-Test', 'Test-Unit'] );

		$expectedHeaders = [
			'X-Test'     => [
				'Unit-Test',
			],
			'X-Multiple' => [
				'Unit-Test',
				'Test-Unit',
			],
		];

		$expectedHeaderLineXTest     = 'Unit-Test';
		$expectedHeaderLineXMultiple = 'Unit-Test,Test-Unit';

		self::assertSame( $expectedHeaders, $response->getHeaders() );
		self::assertSame( $expectedHeaders['X-Test'], $response->getHeader( 'X-Test' ) );
		self::assertSame( $expectedHeaders['X-Multiple'], $response->getHeader( 'X-Multiple' ) );
		self::assertSame( $expectedHeaderLineXTest, $response->getHeaderLine( 'X-Test' ) );
		self::assertSame( $expectedHeaderLineXMultiple, $response->getHeaderLine( 'X-Multiple' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithAddedHeader() : void
	{
		$response = Response::new()
		                    ->withHeader( 'X-Test', 'Unit-Test' )
		                    ->withAddedHeader( 'X-Test', 'Test-Unit' )
		                    ->withAddedHeader( 'X-Custom', 'Value' );

		$expectedHeaders = [
			'X-Test'   => [
				'Unit-Test',
				'Test-Unit',
			],
			'X-Custom' => [
				'Value',
			],
		];

		$expectedHeaderLineXTest = 'Unit-Test,Test-Unit';

		self::assertSame( $expectedHeaders, $response->getHeaders() );
		self::assertSame( $expectedHeaders['X-Test'], $response->getHeader( 'X-Test' ) );
		self::assertSame( $expectedHeaderLineXTest, $response->getHeaderLine( 'X-Test' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testWithoutHeader() : void
	{
		$response = Response::new()->withoutHeader( 'Status' );

		self::assertSame( [], $response->getHeaders() );
		self::assertSame( [], $response->getHeader( 'Status' ) );
		self::assertSame( '', $response->getHeaderLine( 'Status' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithBody() : void
	{
		$body     = Stream::newWithContent( 'Content' );
		$response = Response::new()->withBody( $body );

		self::assertSame( 'Content', (string)$response->getBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testWithProtocolVersion() : void
	{
		$response = Response::new()->withProtocolVersion( 'HTTP/2' );

		self::assertSame( 'HTTP/2', $response->getProtocolVersion() );
		self::assertSame( 200, $response->getStatusCode() );
		self::assertSame( 'OK', $response->getReasonPhrase() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testNew() : void
	{
		$response = Response::new();

		# Check for default values after instantiation
		self::assertSame( 'HTTP/1.1', $response->getProtocolVersion() );
		self::assertSame( 200, $response->getStatusCode() );
		self::assertSame( 'OK', $response->getReasonPhrase() );
		self::assertSame( '', $response->getHeaderLine( 'UnknownHeaderKey' ) );
		self::assertSame( '', (string)$response->getBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithStatus() : void
	{
		$response = Response::new()->withStatus( 404, 'Not Found' );

		self::assertSame( 'HTTP/1.1', $response->getProtocolVersion() );
		self::assertSame( 404, $response->getStatusCode() );
		self::assertSame( 'Not Found', $response->getReasonPhrase() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testNewWithContent() : void
	{
		$response = Response::newWithContent( 'Unit-Test' );

		self::assertSame( 200, $response->getStatusCode() );
		self::assertSame( 'OK', $response->getReasonPhrase() );
		self::assertSame( 'Unit-Test', (string)$response->getBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException|RuntimeException
	 */
	public function testRedirect() : void
	{
		$response = Response::redirect( Uri::fromString( 'https://example.com:443/unit/test' ), 302 );

		$expectedBody = <<<EOF
						<!DOCTYPE html>
						<html lang="en">
						<head>
						   <title>Redirect 302</title>
						   <meta http-equiv="refresh" content="0; 
						   url=https://example.com/unit/test">
						</head>
						<body>
						   <p>Redirecting to:
						   <a href="https://example.com/unit/test">https://example.com/unit/test</a></p>
						</body>
						</html>
						EOF;

		self::assertSame( 302, $response->getStatusCode() );
		self::assertSame( 'Found', $response->getReasonPhrase() );
		self::assertSame( 'https://example.com/unit/test', $response->getHeaderLine( 'Location' ) );
		self::assertSame( 'text/html; charset=utf-8', $response->getHeaderLine( 'Content-Type' ) );
		self::assertSame( $expectedBody, (string)$response->getBody() );
	}
}
