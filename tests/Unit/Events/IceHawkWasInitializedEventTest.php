<?php
namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;

/**
 * Class IceHawkWasInitializedEventTest
 * @package IceHawk\IceHawk\Tests\Unit\Events
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
