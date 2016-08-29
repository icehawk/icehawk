<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\HandlingWriteRequestEvent;
use IceHawk\IceHawk\Requests\WriteRequest;

class HandlingWriteRequestEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$writeRequest = new WriteRequest( $requestInfo, [], '' );

		$event = new HandlingWriteRequestEvent( $writeRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( [], $event->getInputData() );
		$this->assertSame( '', $event->getBody() );
		$this->assertSame( [], $event->getUploadedFiles() );
	}
}
