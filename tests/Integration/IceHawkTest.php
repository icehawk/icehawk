<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Integration;

use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCanCreateInstanceWithDependencies() : void
	{
		self::assertInstanceOf( IceHawk::class, IceHawk::newWithDependencies( $this->getDeps() ) );
	}

	private function getDeps() : ResolvesDependencies
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
}
