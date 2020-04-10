<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Tests\Unit\Types\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Types\Stubs\RequestHandlerImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStrings() : void
	{
		$middlewareClassNames = [MiddlewareImplementation::class];
		$route                = Route::newFromStrings(
			'GET',
			'/unit/test',
			RequestHandlerImplementation::class,
			...$middlewareClassNames
		);

		$this->assertEquals(
			array_map(
				fn( string $item ) : MiddlewareClassName => MiddlewareClassName::newFromString( $item ),
				$middlewareClassNames
			),
			$route->getMiddlewareClassNames()
		);
		$this->assertSame( RequestHandlerImplementation::class, $route->getRequestHandlerClassName()->toString() );
		$this->assertNull( $route->getModifiedRequest() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestSucceeds() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'/unit/test',
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$this->assertTrue( $route->matchesRequest( $request ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestFailsForHttpMethodMismatch() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'/unit/test',
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$this->assertFalse( $route->matchesRequest( $request ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestFailsForPatternMismatch() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'/not-matching',
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class,
			);

		$this->assertFalse( $route->matchesRequest( $request ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetModifiedRequest() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();
		$route   = Route::newFromStrings(
			'GET',
			'/unit/(?<testKey>.*)',
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class,
			);

		$this->assertTrue( $route->matchesRequest( $request ) );

		$this->assertSame( 'test', $route->getModifiedRequest()->getQueryParams()['testKey'] );
		$this->assertNull( $route->getModifiedRequest()->getParsedBody()['testKey'] );
	}
}
