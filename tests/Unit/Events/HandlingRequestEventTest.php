<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;

class HandlingRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();
		$getRequest  = new ReadRequest( $requestInfo, [ ] );

		$event = new HandlingReadRequestEvent( $getRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $getRequest, $event->getRequest() );
	}
}
