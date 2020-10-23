<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Middlewares\OptionsMiddleware;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Stubs\PassThroughMiddleware;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use function function_exists;
use function http_response_code;
use function session_id;
use function session_start;
use function session_status;
use function xdebug_get_headers;
use const PHP_SESSION_ACTIVE;
use const PHP_SESSION_NONE;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testHandleRequestUsesFallbackRequestHandlerIfNoRoutesWereSet() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithNoRoutes() );

		$this->expectException( RequestHandlingFailedException::class );
		$this->expectExceptionMessage( 'No responder found.' );
		$this->expectExceptionCode( 404 );

		$iceHawk->handleRequest( Request::fromGlobals() );
	}

	/**
	 * @param array<string> $expectedHeaders
	 *
	 * @throws ExpectationFailedException
	 */
	private function assertHeaders( array $expectedHeaders ) : void
	{
		if ( function_exists( 'xdebug_get_headers' ) )
		{
			/** @noinspection ForgottenDebugOutputInspection */
			self::assertSame( $expectedHeaders, xdebug_get_headers() );
		}
	}

	private function getDepsWithNoRoutes() : ResolvesDependencies
	{
		return new class implements ResolvesDependencies {
			public function getRoutes() : Routes
			{
				return Routes::new();
			}

			public function getAppMiddlewares() : MiddlewareClassNames
			{
				return MiddlewareClassNames::new();
			}

			public function resolveMiddleware( MiddlewareClassName $middlewareClassName ) : MiddlewareInterface
			{
				return new MiddlewareImplementation();
			}
		};
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testCanHandleGetRequestWithRouteDefaults() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/unit/test/defaults';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithRoutes() );

		$this->expectException( RequestHandlingFailedException::class );
		$this->expectExceptionMessage( 'No responder found.' );
		$this->expectExceptionCode( 404 );

		$iceHawk->handleRequest( Request::fromGlobals() );
	}

	private function getDepsWithRoutes() : ResolvesDependencies
	{
		return new class implements ResolvesDependencies {
			public function getRoutes() : Routes
			{
				return Routes::new(
					Route::get( '/get/unit/test/defaults' ),
					Route::post( '/post/unit/test/defaults' ),
					Route::post( '/post/unit/test/one-middleware', MiddlewareImplementation::class ),
					Route::get( '/get/unit/test/one-middleware', MiddlewareImplementation::class )
				);
			}

			public function resolveMiddleware( MiddlewareClassName $middlewareClassName ) : MiddlewareInterface
			{
				return new MiddlewareImplementation();
			}

			public function getAppMiddlewares() : MiddlewareClassNames
			{
				return MiddlewareClassNames::new();
			}
		};
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testCanHandlePostRequestWithRouteDefaults() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/post/unit/test/defaults';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithRoutes() );

		$this->expectException( RequestHandlingFailedException::class );
		$this->expectExceptionMessage( 'No responder found.' );
		$this->expectExceptionCode( 404 );

		$iceHawk->handleRequest( Request::fromGlobals() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testCanHandlePostRequestWithDefaultHandlerAndOneMiddlewares() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/post/unit/test/one-middleware';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithRoutes() );

		$this->expectOutputString( '' );

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'X-ID: ' . MiddlewareImplementation::class,
		];

		$this->assertHeaders( $expectedHeaders );
		self::assertSame( 200, http_response_code() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testCanHandleOptionsRequestAndReceiveAcceptedMethods() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/unit/test/defaults';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithRoutes() );

		$this->expectOutputString( '' );

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'Allow: GET,CONNECT,OPTIONS,TRACE,HEAD',
		];

		$this->assertHeaders( $expectedHeaders );
		self::assertSame( 204, http_response_code() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testCanAddAppMiddlewares() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/app/middlewares';

		$iceHawk = IceHawk::newWithDependencies( $this->getDependenciesWithAppMiddlewares() );

		$this->expectOutputString( '' );

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'X-ID: ' . PassThroughMiddleware::class . ',' . MiddlewareImplementation::class,
		];

		$this->assertHeaders( $expectedHeaders );
		self::assertSame( 200, http_response_code() );
	}

	private function getDependenciesWithAppMiddlewares() : ResolvesDependencies
	{
		return new class implements ResolvesDependencies {
			public function getAppMiddlewares() : MiddlewareClassNames
			{
				return MiddlewareClassNames::newFromStrings(
					PassThroughMiddleware::class,
					MiddlewareImplementation::class,
				);
			}

			public function getRoutes() : Routes
			{
				return Routes::new(
					Route::get( '/get/app/middlewares' )
				);
			}

			public function resolveMiddleware( MiddlewareClassName $middlewareClassName ) : MiddlewareInterface
			{
				switch ( true )
				{
					case $middlewareClassName->equalsString( PassThroughMiddleware::class ):
						return new PassThroughMiddleware();

					case $middlewareClassName->equalsString( MiddlewareImplementation::class ):
						return new MiddlewareImplementation();

					default:
						throw new RuntimeException( 'Middleware not implemented.' );
				}
			}
		};
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testSessionIsWrittenAndClosedBeforeResponding() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/unit/test/one-middleware';

		session_start();
		$sessionId = session_id();

		self::assertSame( PHP_SESSION_ACTIVE, session_status() );

		IceHawk::newWithDependencies( $this->getDepsWithRoutes() )
		       ->handleRequest( Request::fromGlobals() );

		$this->assertHeaders(
			[
				"Set-Cookie: PHPSESSID={$sessionId}; path=/",
				'Expires: Thu, 19 Nov 1981 08:52:00 GMT',
				'Cache-Control: no-store, no-cache, must-revalidate',
				'Pragma: no-cache',
				'X-ID: IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation',
			]
		);

		self::assertSame( PHP_SESSION_NONE, session_status() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testHeadRequestsDoNotProduceOutput() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/unit/test/one-middleware';

		$this->expectOutputString( '' );

		IceHawk::newWithDependencies( $this->getDepsWithRoutes() )
		       ->handleRequest( Request::fromGlobals() );

		$this->assertHeaders(
			[
				'X-ID: ' . MiddlewareImplementation::class,
				'Content-Length: 0',
			]
		);

		self::assertSame( 200, http_response_code() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testTraceRequestRespondsWithRequestBody() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/get/unit/test/one-middleware';

		$this->expectOutputString( 'Unit-Test' );

		IceHawk::newWithDependencies( $this->getDepsWithRoutes() )
		       ->handleRequest( Request::fromGlobals()->withBody( Stream::newWithContent( 'Unit-Test' ) ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testTraceRequestListsAllProcessedMiddlewaresInHeader() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'TRACE';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/post/unit/test/one-middleware';

		$this->expectOutputString( 'Unit-Test' );

		IceHawk::newWithDependencies( $this->getDepsWithRoutes() )
		       ->handleRequest( Request::fromGlobals()->withBody( Stream::newWithContent( 'Unit-Test' ) ) );

		$this->assertHeaders(
			[
				'X-ID: ' . MiddlewareImplementation::class,
				'X-IceHawk-Trace: ' . MiddlewareImplementation::class . ',' . OptionsMiddleware::class,
				'Content-Type: message/http',
			]
		);
		self::assertSame( 200, http_response_code() );
	}
}
