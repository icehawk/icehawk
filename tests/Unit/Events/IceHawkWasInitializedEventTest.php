<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\RequestInfo;

/**
 * Class IceHawkWasInitializedEventTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Events
 */
class IceHawkWasInitializedEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();

		$event = new IceHawkWasInitializedEvent( $requestInfo );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
	}
}