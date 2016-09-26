<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\HandlingReadRequestEvent;
use IceHawk\IceHawk\Requests\ReadRequest;
use IceHawk\IceHawk\Requests\ReadRequestInput;

class HandlingReadRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new ReadRequestInput( [] );
		$readRequest  = new ReadRequest( $requestInfo, $requestInput );

		$event = new HandlingReadRequestEvent( $readRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}
