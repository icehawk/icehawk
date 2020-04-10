<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit;

use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Tests\Unit\Types\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use function function_exists;
use function xdebug_get_headers;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testHandleRequestUsesFallbackRequestHandlerIfNoRoutesWereSet() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithNoRoutes() );

		$this->expectOutputString(
			"Exception occurred: Could not find route for request: https://example.com/unit/test\n"
			. 'Tried to handle request for URI: https://example.com/unit/test'
		);

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'Status: HTTP/1.1 404 Not Found',
			'Content-Type: text/plain; charset=utf-8',
		];

		$this->assertHeaders( $expectedHeaders );
	}

	/**
	 * @param array $expectedHeaders
	 *
	 * @throws ExpectationFailedException
	 */
	private function assertHeaders( array $expectedHeaders ) : void
	{
		if ( function_exists( 'xdebug_get_headers' ) )
		{
			/** @noinspection ForgottenDebugOutputInspection */
			$this->assertSame( $expectedHeaders, xdebug_get_headers() );
		}
	}

	private function getDepsWithNoRoutes() : ResolvesDependencies
	{
		return new class implements ResolvesDependencies {
			public function getRoutes() : RouteCollection
			{
				return RouteCollection::new();
			}

			public function resolveRequestHandler(
				RequestHandlerClassName $handlerClassName,
				MiddlewareClassName ...$middlewareClassNames
			) : RequestHandlerInterface
			{
				return FallbackRequestHandler::newWithMessage( 'Fallback active.' );
			}
		};
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @runInSeparateProcess
	 */
	public function testCanHandleRequestBasedOnRouteDefaults() : void
	{
		$_SERVER['HTTPS']          = 'On';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test/defaults';

		$iceHawk = IceHawk::newWithDependencies( $this->getDepsWithRoutesFromConfigArray() );

		$this->expectOutputString(
			"Queue handler with fallback active.\n"
			. 'Tried to handle request for URI: https://example.com/unit/test/defaults'
		);

		$iceHawk->handleRequest( Request::fromGlobals() );

		$expectedHeaders = [
			'Status: HTTP/1.1 404 Not Found',
			'Content-Type: text/plain; charset=utf-8',
		];

		$this->assertHeaders( $expectedHeaders );
	}

	private function getDepsWithRoutesFromConfigArray() : ResolvesDependencies
	{
		return new class implements ResolvesDependencies {
			public function getRoutes() : RouteCollection
			{
				return RouteCollection::fromConfigArray(
					[
						'/unit/test/defaults' => [],
					]
				);
			}

			public function resolveRequestHandler(
				RequestHandlerClassName $handlerClassName,
				MiddlewareClassName ...$middlewareClassNames
			) : RequestHandlerInterface
			{
				switch ( true )
				{
					case $handlerClassName->equalsString( QueueRequestHandler::class ):
						$handler = QueueRequestHandler::newWithFallbackHandler(
							FallbackRequestHandler::newWithMessage( 'Queue handler with fallback active.' )
						);

						foreach ( $middlewareClassNames as $middlewareClassName )
						{
							$handler->add( $this->resolveMiddleware( $middlewareClassName ) );
						}

						return $handler;

					default:
						return FallbackRequestHandler::newWithMessage( 'Fallback active.' );
				}
			}

			private function resolveMiddleware( MiddlewareClassName $middlewareClassName ) : MiddlewareInterface
			{
				return new MiddlewareImplementation();
			}
		};
	}
}
