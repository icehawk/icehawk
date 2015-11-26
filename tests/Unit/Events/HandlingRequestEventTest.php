<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;

class HandlingRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();
		$getRequest  = new GetRequest( $requestInfo, [ ] );

		$event = new HandlingRequestEvent( $getRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $getRequest, $event->getRequest() );
	}
}
