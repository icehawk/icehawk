<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\InitializingIceHawkEvent;

class InitializingIceHawkEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();

		$event = new InitializingIceHawkEvent( $requestInfo );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
	}
}
