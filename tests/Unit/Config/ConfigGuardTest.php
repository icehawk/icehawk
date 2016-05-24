<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Config;

use Fortuneglobe\IceHawk\Config\ConfigGuard;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestEventSubscriber;

/**
 * Class ConfigGuardTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Defaults
 */
class ConfigGuardTest extends \PHPUnit_Framework_TestCase
{
	public function invalidSubscriberProvider()
	{
		return [
			[ [ 'invalidSubscriber', new TestEventSubscriber(), null ], [ 0, 2 ] ],
			[ [ '', new \stdClass(), 1, new TestEventSubscriber() ], [ 0, 1, 2 ] ],
			[ [ new TestEventSubscriber(), [ ], 0, true, false ], [ 1, 2, 3, 4 ] ],
		];
	}

	/**
	 * @dataProvider invalidSubscriberProvider
	 */
	public function testInvalidSubscribersThrowsException( array $invalidSubscribers, array $invalidKeys )
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( $invalidSubscribers );

		try
		{
			$configGuard = new ConfigGuard( $config );
			$configGuard->validate();

			$this->fail( 'No Exception thrown' );
		}
		catch ( InvalidEventSubscriberCollection $ex )
		{
			$this->assertEquals( $invalidKeys, $ex->getInvalidKeys() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown' );
		}
	}
}