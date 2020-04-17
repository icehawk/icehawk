<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Dependencies;

use IceHawk\IceHawk\Dependencies\AbstractDependencies;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Tests\Unit\Stubs\RequestHandlerImplementation;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

final class AbstractDependenciesTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testCanGetSameInstanceFromPool() : void
	{
		$deps = new class extends AbstractDependencies {
			public function getRoutes() : RouteCollection
			{
				return RouteCollection::new();
			}

			public function resolveRequestHandler(
				RequestHandlerClassName $handlerClassName,
				MiddlewareClassName ...$middlewareClassNames
			) : RequestHandlerInterface
			{
				return new RequestHandlerImplementation();
			}

			public function getAnObject() : stdClass
			{
				return $this->getInstance( __METHOD__, fn() => new stdClass() );
			}
		};

		$this->assertSame( $deps->getAnObject(), $deps->getAnObject() );
	}
}
