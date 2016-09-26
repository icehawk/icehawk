<?php
namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\Requests\WriteRequest;
use IceHawk\IceHawk\Requests\WriteRequestInput;

/**
 * Class WriteRequestWasHandledEventTest
 * @package IceHawk\IceHawk\Tests\Unit\Events
 */
class WriteRequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new WriteRequestInput( '', [] );
		$writeRequest = new WriteRequest( $requestInfo, $requestInput );

		$event = new WriteRequestWasHandledEvent( $writeRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}
