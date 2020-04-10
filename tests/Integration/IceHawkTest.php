<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Integration;

use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCanCreateInstanceWithDependencies() : void
	{
		$this->assertInstanceOf( IceHawk::class, IceHawk::newWithDependencies( $this->getDeps() ) );
	}

	private function getDeps() : ResolvesDependencies
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
}
