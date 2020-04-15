<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\RequestHandlers\OptionsRequestHandler;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\RouteCollection;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class OptionsRequestHandlerTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testHandle() : void
	{
		$_SERVER['HTTPS']          = true;
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$routes = RouteCollection::new(
			Route::get( '/unit/test' ),
			Route::post( '/unit/test' ),
			Route::put( '/test/unit' )
		);

		$response = OptionsRequestHandler::newWithRoutes( $routes )->handle( Request::fromGlobals() );

		$acceptHeader   = $response->getHeader( 'Accept' );
		$expectedHeader = [
			'GET',
			'HEAD',
			'POST',
			'OPTIONS',
			'TRACE',
			'CONNECT',
		];

		sort( $acceptHeader );
		sort( $expectedHeader );

		$this->assertSame( $expectedHeader, $acceptHeader );
	}
}
