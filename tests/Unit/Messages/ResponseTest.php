<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\Stream;
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
			'Status'     => [
				'HTTP/1.1 200 OK',
			],
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

		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedHeaders['X-Test'], $response->getHeader( 'X-Test' ) );
		$this->assertSame( $expectedHeaders['X-Multiple'], $response->getHeader( 'X-Multiple' ) );
		$this->assertSame( $expectedHeaderLineXTest, $response->getHeaderLine( 'X-Test' ) );
		$this->assertSame( $expectedHeaderLineXMultiple, $response->getHeaderLine( 'X-Multiple' ) );
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
			'Status'   => [
				'HTTP/1.1 200 OK',
			],
			'X-Test'   => [
				'Unit-Test',
				'Test-Unit',
			],
			'X-Custom' => [
				'Value',
			],
		];

		$expectedHeaderLineXTest = 'Unit-Test,Test-Unit';

		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedHeaders['X-Test'], $response->getHeader( 'X-Test' ) );
		$this->assertSame( $expectedHeaderLineXTest, $response->getHeaderLine( 'X-Test' ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithoutHeader() : void
	{
		$response = Response::new()->withoutHeader( 'Status' );

		$this->assertSame( [], $response->getHeaders() );
		$this->assertSame( [], $response->getHeader( 'Status' ) );
		$this->assertSame( '', $response->getHeaderLine( 'Status' ) );
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

		$this->assertSame( 'Content', (string)$response->getBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithProtocolVersion() : void
	{
		$response = Response::new()->withProtocolVersion( 'HTTP/2' );

		$expectedHeaders = [
			'Status' => [
				'HTTP/2 200 OK',
			],
		];

		$expectedHeaderLine = 'HTTP/2 200 OK';

		$this->assertSame( 'HTTP/2', $response->getProtocolVersion() );
		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'OK', $response->getReasonPhrase() );
		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedHeaders['Status'], $response->getHeader( 'Status' ) );
		$this->assertSame( $expectedHeaderLine, $response->getHeaderLine( 'Status' ) );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testNew() : void
	{
		$response = Response::new();

		$expectedHeaders = [
			'Status' => [
				'HTTP/1.1 200 OK',
			],
		];

		$expectedHeaderLine = 'HTTP/1.1 200 OK';

		# Check for default values after instantiation
		$this->assertSame( 'HTTP/1.1', $response->getProtocolVersion() );
		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'OK', $response->getReasonPhrase() );
		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedHeaders['Status'], $response->getHeader( 'Status' ) );
		$this->assertSame( $expectedHeaderLine, $response->getHeaderLine( 'Status' ) );
		$this->assertSame( '', $response->getHeaderLine( 'UnknownHeaderKey' ) );
		$this->assertSame( '', (string)$response->getBody() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testWithStatus() : void
	{
		$response = Response::new()->withStatus( 404, 'Not Found' );

		$expectedHeaders = [
			'Status' => [
				'HTTP/1.1 404 Not Found',
			],
		];

		$expectedHeaderLine = 'HTTP/1.1 404 Not Found';

		$this->assertSame( 'HTTP/1.1', $response->getProtocolVersion() );
		$this->assertSame( 404, $response->getStatusCode() );
		$this->assertSame( 'Not Found', $response->getReasonPhrase() );
		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedHeaders['Status'], $response->getHeader( 'Status' ) );
		$this->assertSame( $expectedHeaderLine, $response->getHeaderLine( 'Status' ) );
	}
}