<?php
namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\Requests\WriteRequest;

/**
 * Class WriteRequestWasHandledEventTest
 * @package IceHawk\IceHawk\Tests\Unit\Events
 */
class WriteRequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$writeRequest = new WriteRequest( $requestInfo, [], '' );

		$event = new WriteRequestWasHandledEvent( $writeRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( [], $event->getInputData() );
		$this->assertSame( '', $event->getBody() );
		$this->assertSame( [], $event->getUploadedFiles() );
	}
}
