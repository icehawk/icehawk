<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\InitializingIceHawkEvent;

class InitializingIceHawkEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();

		$event = new InitializingIceHawkEvent( $requestInfo );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
	}
}
