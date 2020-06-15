<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Routing\Interfaces\ResolvesRouteToMiddlewares;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use function iterator_to_array;

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
			'^/unit/test$',
			...$middlewareClassNames
		);

		$this->assertEquals(
			MiddlewareClassNames::newFromStrings( ...$middlewareClassNames ),
			$route->getMiddlewareClassNames()
		);
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
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'^/unit/test$',
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
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'^/unit/test$',
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
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();

		$route = Route::newFromStrings(
			'GET',
			'^/not-matching$',
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
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$request = Request::fromGlobals();
		$route   = Route::newFromStrings(
			'GET',
			'^/unit/(?<testKey>.*)$',
			MiddlewareImplementation::class,
		);

		$this->assertTrue( $route->matchesRequest( $request ) );

		/** @var ServerRequestInterface $modifiedRequest */
		$modifiedRequest = $route->getModifiedRequest();

		$this->assertSame( 'test', $modifiedRequest->getQueryParams()['testKey'] );

		/** @var array<string, mixed> $parsedBody */
		$parsedBody = $modifiedRequest->getParsedBody();

		$this->assertNull( $parsedBody['testKey'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetMatchesRequests() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$route = Route::get( '^/unit/test$' );

		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		# Also matches HEAD, OPTIONS, CONNECT & TRACE requests
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'CONNECT';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testPostMatchesRequests() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$route = Route::post( '^/unit/test$' );

		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		# Does not match HEAD request
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertFalse( $route->matchesRequest( Request::fromGlobals() ) );

		# Also matches OPTIONS, CONNECT & TRACE requests
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'CONNECT';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testPutMatchesRequests() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$route = Route::put( '^/unit/test$' );

		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		# Does not match HEAD request
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertFalse( $route->matchesRequest( Request::fromGlobals() ) );

		# Also matches OPTIONS, CONNECT & TRACE requests
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'CONNECT';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testPatchMatchesRequests() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'PATCH';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$route = Route::patch( '^/unit/test$' );

		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		# Does not match HEAD request
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertFalse( $route->matchesRequest( Request::fromGlobals() ) );

		# Also matches OPTIONS, CONNECT & TRACE requests
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'CONNECT';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testDeleteMatchesRequests() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$route = Route::delete( '/unit/test' );

		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		# Does not match HEAD request
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertFalse( $route->matchesRequest( Request::fromGlobals() ) );

		# Also matches OPTIONS, CONNECT & TRACE requests
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'CONNECT';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );

		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		$this->assertTrue( $route->matchesRequest( Request::fromGlobals() ) );
	}

	/**
	 * @param ResolvesRouteToMiddlewares $route
	 * @param array<int, HttpMethod>     $acceptedMethods
	 *
	 * @dataProvider acceptedHttpMethodsRouteProvider
	 * @throws ExpectationFailedException
	 */
	public function testGetAcceptedHttpMethods( ResolvesRouteToMiddlewares $route, array $acceptedMethods ) : void
	{
		$routeMethods = iterator_to_array( $route->getAcceptedHttpMethods()->getIterator(), false );
		sort( $routeMethods );
		sort( $acceptedMethods );

		$this->assertEquals( $acceptedMethods, $routeMethods );
	}

	/**
	 * @return array|array[]
	 * @throws InvalidArgumentException
	 */
	public function acceptedHttpMethodsRouteProvider() : array
	{
		return [
			'GET route'    => [
				'route'           => Route::get( '/unit/test' ),
				'acceptedMethods' => [
					HttpMethod::get(),
					HttpMethod::head(),
					HttpMethod::trace(),
					HttpMethod::options(),
					HttpMethod::connect(),
				],
			],
			'POST route'   => [
				'route'           => Route::post( '/unit/test' ),
				'acceptedMethods' => [
					HttpMethod::post(),
					HttpMethod::trace(),
					HttpMethod::options(),
					HttpMethod::connect(),
				],
			],
			'PUT route'    => [
				'route'           => Route::put( '/unit/test' ),
				'acceptedMethods' => [
					HttpMethod::put(),
					HttpMethod::trace(),
					HttpMethod::options(),
					HttpMethod::connect(),
				],
			],
			'PATCH route'  => [
				'route'           => Route::patch( '/unit/test' ),
				'acceptedMethods' => [
					HttpMethod::patch(),
					HttpMethod::trace(),
					HttpMethod::options(),
					HttpMethod::connect(),
				],
			],
			'DELETE route' => [
				'route'           => Route::delete( '/unit/test' ),
				'acceptedMethods' => [
					HttpMethod::delete(),
					HttpMethod::trace(),
					HttpMethod::options(),
					HttpMethod::connect(),
				],
			],
		];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesAgainstFullUri() : void
	{
		$uri   = Uri::fromString( 'https://example.com/unit/test?uri=match' );
		$route = Route::get( '/unit/test\?uri=(?<uri>.+)$' )->matchAgainstFullUri();

		$this->assertTrue( $route->matchesUri( $uri ) );

		$route = Route::get( '/unit/test\?match=(?<match>.+)$' )->matchAgainstFullUri();

		$this->assertFalse( $route->matchesUri( $uri ) );
	}
}
