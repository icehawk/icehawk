<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\HandlingWriteRequestEvent;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;

class HandlingWriteRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new WriteRequestInput( '', [ ] );
		$writeRequest = new WriteRequest( $requestInfo, $requestInput );

		$event = new HandlingWriteRequestEvent( $writeRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}
