<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;

class HandlingReadRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new ReadRequestInput( [ ] );
		$readRequest  = new ReadRequest( $requestInfo, $requestInput );

		$event = new HandlingReadRequestEvent( $readRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}
