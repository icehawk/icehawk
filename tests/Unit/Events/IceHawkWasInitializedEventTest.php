<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;

/**
 * Class IceHawkWasInitializedEventTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Events
 */
class IceHawkWasInitializedEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();

		$event = new IceHawkWasInitializedEvent( $requestInfo );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
	}
}
