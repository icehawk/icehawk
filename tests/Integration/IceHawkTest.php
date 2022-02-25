<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Integration;

use IceHawk\IceHawk\Dependencies\Container;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ConfigInterface;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Routing\Routes;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCanCreateInstanceWithDependencies() : void
	{
		self::assertInstanceOf( IceHawk::class, IceHawk::new( $this->getConfig(), Container::new() ) );
	}

	private function getConfig() : ConfigInterface
	{
		return new class implements ConfigInterface
		{
			#[Pure]
			public function getRoutes() : RoutesInterface
			{
				return Routes::new();
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
}
