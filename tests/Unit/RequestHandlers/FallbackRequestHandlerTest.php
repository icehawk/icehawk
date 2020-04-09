<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FallbackRequestHandlerTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testNewWithMessage() : void
	{
		$_SERVER['HTTPS'] = true;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test/fallback';

		$response = FallbackRequestHandler::newWithMessage( 'Unit-Test' )->handle( Request::fromGlobals() );

		$expectedBody = "Unit-Test\nTried to handle request for URI: https://example.com/unit/test/fallback";

		$this->assertSame( 404, $response->getStatusCode() );
		$this->assertSame( 'Not Found', $response->getReasonPhrase() );
		$this->assertSame( 'text/plain; charset=utf-8', $response->getHeaderLine( 'Content-Type' ) );
		$this->assertSame( $expectedBody, (string)$response->getBody() );
	}
}
