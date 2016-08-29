<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\HandlingReadRequestEvent;
use IceHawk\IceHawk\Requests\ReadRequest;

class HandlingReadRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$readRequest  = new ReadRequest( $requestInfo, [] );

		$event = new HandlingReadRequestEvent( $readRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( [], $event->getInputData() );
	}
}
