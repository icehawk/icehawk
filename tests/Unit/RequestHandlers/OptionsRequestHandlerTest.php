<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\RequestHandlers\OptionsRequestHandler;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Tests\Unit\Stubs\RequestHandlerImplementation;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class OptionsRequestHandlerTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testHandleReturnsResponseForOptionsRequest() : void
	{
		$_SERVER['HTTPS']          = true;
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$routes = RouteCollection::new(
			Route::get( '/unit/test' ),
			Route::post( '/unit/test' ),
			Route::put( '/test/unit' )
		);

		$response = OptionsRequestHandler::new( $routes, new RequestHandlerImplementation() )
		                                 ->handle( Request::fromGlobals() );

		$allowHeader    = $response->getHeader( 'Allow' );
		$expectedHeader = [
			'GET',
			'HEAD',
			'POST',
			'OPTIONS',
			'TRACE',
			'CONNECT',
		];

		sort( $allowHeader );
		sort( $expectedHeader );

		$this->assertSame( $expectedHeader, $allowHeader );
		$this->assertSame( 204, $response->getStatusCode() );
		$this->assertSame( 'No Content', $response->getReasonPhrase() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testHandleReturnsResponseFromRequestHandlerForNonOptionsRequest() : void
	{
		$_SERVER['HTTPS']          = true;
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$routes = RouteCollection::new(
			Route::get( '/unit/test' ),
		);

		$response = OptionsRequestHandler::new( $routes, new RequestHandlerImplementation() )
		                                 ->handle( Request::fromGlobals() );

		$this->assertSame( [RequestHandlerImplementation::class], $response->getHeader( 'X-ID' ) );
		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'OK', $response->getReasonPhrase() );
	}
}
