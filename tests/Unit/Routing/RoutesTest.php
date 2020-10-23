<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Routing\NullRoute;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RoutesTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetIterator() : void
	{
		$route = Route::newFromStrings(
			'GET',
			'/unit/test',
			MiddlewareImplementation::class,
		);

		$routes = Routes::new( $route );

		self::assertSame( $route, $routes->getIterator()->current() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testAdd() : void
	{
		$route = Route::newFromStrings(
			'GET',
			'/unit/test',
			MiddlewareImplementation::class
		);

		$routes = Routes::new();

		self::assertCount( 0, $routes );
		self::assertCount( 0, $routes->getIterator() );

		$routes->add( $route );

		self::assertCount( 1, $routes );
		self::assertCount( 1, $routes->getIterator() );

		self::assertSame( $route, $routes->getIterator()->current() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testAddMultipleRoutes() : void
	{
		$route1 = Route::newFromStrings(
			'GET',
			'/unit/test',
			MiddlewareImplementation::class
		);

		$route2 = Route::newFromStrings(
			'POST',
			'/unit/test',
			MiddlewareImplementation::class
		);

		$routes = Routes::new();
		$routes->add( $route1, $route2 );

		self::assertCount( 2, $routes );
		self::assertCount( 2, $routes->getIterator() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testCount() : void
	{
		self::assertCount( 0, Routes::new() );
		self::assertSame( 0, Routes::new()->count() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testFindMatchingRouteForRequest() : void
	{
		$route = Route::newFromStrings(
			'GET',
			'/unit/test',
			MiddlewareImplementation::class
		);

		$routes = Routes::new( $route );

		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();

		self::assertSame( $route, $routes->findMatchingRouteForRequest( $request ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testFindMatchingRouteForRequestReturnsNullRouteIfNoRouteWasFound() : void
	{
		$routes = Routes::new();

		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();

		self::assertEquals( NullRoute::new( $request ), $routes->findMatchingRouteForRequest( $request ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		$routes = Routes::new();
		self::assertCount( 0, $routes );
		self::assertCount( 0, $routes->getIterator() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testFindAcceptedHttpMethodsForUri() : void
	{
		$uri    = Uri::fromString( 'https://example.com/unit/test' );
		$routes = Routes::new(
		# matching routes
			Route::get( '/unit/test' ),
			Route::post( '/unit/test' ),
			# not-matching route
			Route::put( '/test/unit' ),
		);

		$expectedHttpMethods = [
			HttpMethod::get(),
			HttpMethod::head(),
			HttpMethod::post(),
			HttpMethod::connect(),
			HttpMethod::options(),
			HttpMethod::trace(),
		];

		$acceptedMethods = $routes->findAcceptedHttpMethodsForUri( $uri );

		sort( $expectedHttpMethods );
		sort( $acceptedMethods );

		self::assertEquals( $expectedHttpMethods, $acceptedMethods );
	}
}
