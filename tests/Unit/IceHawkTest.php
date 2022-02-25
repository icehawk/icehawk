<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit;

use IceHawk\IceHawk\Dependencies\Container;
use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ConfigInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Middlewares\OptionsMiddleware;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Tests\Unit\Stubs\PassThroughMiddleware;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
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

		$iceHawk = IceHawk::new( $this->getConfigWithoutRoutes(), Container::new() );

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

	private function getConfigWithoutRoutes() : ConfigInterface
	{
		return new class implements ConfigInterface
		{
			#[Pure]
			public function getRoutes() : RoutesInterface
			{
				return Routes::new();
			}

			#[Pure]
			public function getAppMiddlewares() : MiddlewareClassNamesInterface
			{
				return MiddlewareClassNames::new();
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

		$iceHawk = IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() );

		$this->expectException( RequestHandlingFailedException::class );
		$this->expectExceptionMessage( 'No responder found.' );
		$this->expectExceptionCode( 404 );

		$iceHawk->handleRequest( Request::fromGlobals() );
	}

	private function getConfigWithRoutes() : ConfigInterface
	{
		return new class implements ConfigInterface
		{
			public function getRoutes() : Routes
			{
				return Routes::new(
					Route::get( '/get/unit/test/defaults' ),
					Route::post( '/post/unit/test/defaults' ),
					Route::post( '/post/unit/test/one-middleware', MiddlewareImplementation::class ),
					Route::get( '/get/unit/test/one-middleware', MiddlewareImplementation::class )
				);
			}

			/**
			 * @return array<string>
			 */
			public function getAppMiddlewares() : array
			{
				return [];
			}
		};
	}

	#[Pure]
	private function getContainerWithMiddlewareImplementation() : ContainerInterface
	{
		return Container::new(
			[
				MiddlewareImplementation::class => fn() => new MiddlewareImplementation(),
			]
		);
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

		$iceHawk = IceHawk::new( $this->getConfigWithRoutes(), Container::new() );

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

		$iceHawk = IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() );

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

		$iceHawk = IceHawk::new( $this->getConfigWithRoutes(), Container::new() );

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

		$iceHawk = IceHawk::new( $this->getConfigWithAppMiddlewares(), $this->getContainerWithAppMiddlewares() );

		$this->expectOutputString( '' );

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'X-ID: ' . PassThroughMiddleware::class . ',' . MiddlewareImplementation::class,
		];

		$this->assertHeaders( $expectedHeaders );
		self::assertSame( 200, http_response_code() );
	}

	private function getConfigWithAppMiddlewares() : ConfigInterface
	{
		return new class implements ConfigInterface
		{
			public function getAppMiddlewares() : MiddlewareClassNamesInterface
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
		};
	}

	#[Pure]
	private function getContainerWithAppMiddlewares() : ContainerInterface
	{
		return Container::new(
			[
				PassThroughMiddleware::class    => fn() => new PassThroughMiddleware(),
				MiddlewareImplementation::class => fn() => new MiddlewareImplementation(),
			]
		);
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

		IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() )
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

		IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() )
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

		IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() )
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

		IceHawk::new( $this->getConfigWithRoutes(), $this->getContainerWithMiddlewareImplementation() )
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
