<?php
namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\ReadRequestWasHandledEvent;
use IceHawk\IceHawk\Requests\ReadRequest;

/**
 * Class ReadRequestWasHandledEventTest
 * @package IceHawk\IceHawk\Tests\Unit\Events
 */
class ReadRequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$readRequest = new ReadRequest( $requestInfo, [] );

		$event = new ReadRequestWasHandledEvent( $readRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( [], $event->getInputData() );
	}
}