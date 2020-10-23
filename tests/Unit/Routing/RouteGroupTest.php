<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\RouteGroup;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class RouteGroupTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testMatchAgainstFullUriAndModifiedRequestFromGroupAndRoute() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v1/get-request?testkey=testvalue';
		$_SERVER['QUERY_STRING'] = 'testkey=testvalue';
		$_GET['testkey']         = 'testvalue';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getFullUriRouteGroup()->matchAgainstFullUri();

		self::assertTrue( $routeGroup->matchesRequest( $request ) );

		/** @var ServerRequestInterface $modifiedRequest */
		$modifiedRequest = $routeGroup->getModifiedRequest();

		self::assertSame( 'v1', $modifiedRequest->getQueryParams()['version'] );
		self::assertSame( 'get', $modifiedRequest->getQueryParams()['method'] );
		self::assertSame( 'testvalue', $modifiedRequest->getQueryParams()['testkey'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchAgainstFullUriAndModifiedRequestFromGroupOnly() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v2/post-request';
		$_SERVER['QUERY_STRING'] = '';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getFullUriRouteGroup()->matchAgainstFullUri();

		self::assertTrue( $routeGroup->matchesRequest( $request ) );

		/** @var ServerRequestInterface $modifiedRequest */
		$modifiedRequest = $routeGroup->getModifiedRequest();

		self::assertSame( 'v2', $modifiedRequest->getQueryParams()['version'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchAgainstFullUriGroupMatchesButRouteDoesnt() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v2/post-request'; # Wrong API version
		$_SERVER['QUERY_STRING'] = '';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getFullUriRouteGroup()->matchAgainstFullUri();

		self::assertFalse( $routeGroup->matchesRequest( $request ) );
		self::assertNull( $routeGroup->getModifiedRequest() );
	}

	/**
	 * @return RouteGroup
	 * @throws InvalidArgumentException
	 */
	private function getFullUriRouteGroup() : RouteGroup
	{
		return RouteGroup::new(
			'^https\://example\.com/api/(?<version>v\d+)/',
			Route::get( '/api/v1/(?<method>[^/]+)-request\?testkey=testvalue$', MiddlewareImplementation::class ),
			Route::post( '^https\://example\.com/api/v2/post-request$', MiddlewareImplementation::class ),
			Route::put( '/api/v3/put-request$', MiddlewareImplementation::class ),
			Route::patch( '/api/v4/patch-request$', MiddlewareImplementation::class ),
			Route::delete( '/api/v5/delete-request$', MiddlewareImplementation::class ),
		);
	}

	/**
	 * @return RouteGroup
	 * @throws InvalidArgumentException
	 */
	private function getRouteGroup() : RouteGroup
	{
		return RouteGroup::new(
			'^/api/(?<version>v\d+)/',
			Route::get( '^/api/v1/(?<method>[^/]+)-request$', MiddlewareImplementation::class ),
			Route::post( '^/api/v2/post-request$', MiddlewareImplementation::class ),
			Route::put( '^/api/v3/put-request$', MiddlewareImplementation::class ),
			Route::patch( '^/api/v4/patch-request$', MiddlewareImplementation::class ),
			Route::delete( '^/api/v5/delete-request$', MiddlewareImplementation::class ),
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestWithModifiedRequestFromGroupAndRoute() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v1/get-request?testkey=testvalue';
		$_SERVER['QUERY_STRING'] = 'testkey=testvalue';
		$_GET['testkey']         = 'testvalue';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getRouteGroup();

		self::assertTrue( $routeGroup->matchesRequest( $request ) );

		/** @var ServerRequestInterface $modifiedRequest */
		$modifiedRequest = $routeGroup->getModifiedRequest();

		self::assertSame( 'v1', $modifiedRequest->getQueryParams()['version'] );
		self::assertSame( 'get', $modifiedRequest->getQueryParams()['method'] );
		self::assertSame( 'testvalue', $modifiedRequest->getQueryParams()['testkey'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestWithModifiedRequestFromGroupOnly() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v2/post-request';
		$_SERVER['QUERY_STRING'] = '';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getRouteGroup();

		self::assertTrue( $routeGroup->matchesRequest( $request ) );

		/** @var ServerRequestInterface $modifiedRequest */
		$modifiedRequest = $routeGroup->getModifiedRequest();

		self::assertSame( 'v2', $modifiedRequest->getQueryParams()['version'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequestGroupMatchesButRouteDoesnt() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v2/put-request'; # Wrong API version
		$_SERVER['QUERY_STRING'] = '';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getRouteGroup();

		self::assertFalse( $routeGroup->matchesRequest( $request ) );
		self::assertNull( $routeGroup->getModifiedRequest() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesFullUri() : void
	{
		$routeGroup = $this->getFullUriRouteGroup()->matchAgainstFullUri();
		$uri        = Uri::fromString( 'https://example.com/api/v2/post-request' );

		self::assertTrue( $routeGroup->matchesUri( $uri ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesUri() : void
	{
		$routeGroup = $this->getRouteGroup();
		$uri        = Uri::fromString( 'https://example.com/api/v2/post-request' );

		self::assertTrue( $routeGroup->matchesUri( $uri ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesUriNot() : void
	{
		# Group does not match, wrong scheme

		$routeGroup = $this->getFullUriRouteGroup();
		$uri        = Uri::fromString( 'http://example.com/api/v2/post-request' );

		self::assertFalse( $routeGroup->matchesUri( $uri ) );

		# Route does not match, wrong API version

		$uri = Uri::fromString( 'https://example.com/api/v1/post-request' );

		self::assertFalse( $routeGroup->matchesUri( $uri ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetAcceptedHttpMethods() : void
	{
		$routeGroup = $this->getRouteGroup();

		self::assertEquals( HttpMethods::all(), $routeGroup->getAcceptedHttpMethods() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetMiddlewareClassNames() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']    = 'example.com';
		$_SERVER['REQUEST_URI']  = '/api/v2/post-request';
		$_SERVER['QUERY_STRING'] = '';

		$request    = Request::fromGlobals();
		$routeGroup = $this->getRouteGroup();

		self::assertTrue( $routeGroup->matchesRequest( $request ) );
		self::assertTrue(
			MiddlewareClassName::newFromString( MiddlewareImplementation::class )->equals(
				$routeGroup->getMiddlewareClassNames()->getIterator()->current()
			)
		);
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws Exception
	 */
	public function testGetMiddlewareClassNamesIsEmptyIfNoRouteWasFound() : void
	{
		$routeGroup = $this->getRouteGroup();

		self::assertCount( 0, $routeGroup->getMiddlewareClassNames() );
	}
}
