<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Exceptions\RouteNotFoundException;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Stubs\RequestHandlerImplementation;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RouteCollectionTest extends TestCase
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
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class,
		);

		$collection = RouteCollection::new( $route );

		$this->assertSame( $route, $collection->getIterator()->current() );
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
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$collection = RouteCollection::new();

		$this->assertCount( 0, $collection );
		$this->assertCount( 0, $collection->getIterator() );

		$collection->add( $route );

		$this->assertCount( 1, $collection );
		$this->assertCount( 1, $collection->getIterator() );

		$this->assertSame( $route, $collection->getIterator()->current() );
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
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$route2 = Route::newFromStrings(
			'POST',
			'/unit/test',
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$collection = RouteCollection::new();
		$collection->add( $route1, $route2 );

		$this->assertCount( 2, $collection );
		$this->assertCount( 2, $collection->getIterator() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testCount() : void
	{
		$this->assertCount( 0, RouteCollection::new() );
		$this->assertSame( 0, RouteCollection::new()->count() );
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
			RequestHandlerImplementation::class,
			MiddlewareImplementation::class
		);

		$collection = RouteCollection::new( $route );

		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();

		$this->assertSame( $route, $collection->findMatchingRouteForRequest( $request ) );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testFindMatchingRouteForRequestThrowsExceptionIfNoRouteWasFound() : void
	{
		$collection = RouteCollection::new();

		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$request = Request::fromGlobals();

		$this->expectException( RouteNotFoundException::class );
		$this->expectExceptionMessage(
			'Could not find route for requested method (GET) and URI: https://example.com/unit/test'
		);

		/** @noinspection UnusedFunctionResultInspection */
		$collection->findMatchingRouteForRequest( $request );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testNew() : void
	{
		$collection = RouteCollection::new();
		$this->assertCount( 0, $collection );
		$this->assertCount( 0, $collection->getIterator() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testFindAcceptedHttpMethodsForUri() : void
	{
		$uri        = Uri::fromString( 'https://example.com/unit/test' );
		$collection = RouteCollection::new(
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

		$acceptedMethods = $collection->findAcceptedHttpMethodsForUri( $uri );

		sort( $expectedHttpMethods );
		sort( $acceptedMethods );

		$this->assertEquals( $expectedHttpMethods, $acceptedMethods );
	}
}
