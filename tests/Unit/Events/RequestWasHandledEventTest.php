<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;

class RequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo = RequestInfo::fromEnv();
		$getRequest  = new GetRequest( [ ] );

		$event = new RequestWasHandledEvent( $requestInfo, $getRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $getRequest, $event->getRequest() );
	}
}
