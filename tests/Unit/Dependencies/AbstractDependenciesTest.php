<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Dependencies;

use IceHawk\IceHawk\Dependencies\AbstractDependencies;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Tests\Unit\Stubs\MiddlewareImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use stdClass;

final class AbstractDependenciesTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testCanGetSameInstanceFromPool() : void
	{
		$deps = new class extends AbstractDependencies {
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

			public function getAnObject() : stdClass
			{
				return $this->getInstance( fn() => new stdClass() );
			}

			public function getAnObjectByIdentifier() : stdClass
			{
				return $this->getInstance( fn() => new stdClass(), 'Identifier' );
			}

			public function getSameObjectByIdentifier() : stdClass
			{
				return $this->getInstance( fn() => new stdClass(), 'Identifier' );
			}
		};

		self::assertSame( $deps->getAnObject(), $deps->getAnObject() );
		self::assertSame( $deps->getAnObjectByIdentifier(), $deps->getSameObjectByIdentifier() );
	}
}
