<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\WriteRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;

/**
 * Class WriteRequestWasHandledEventTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Events
 */
class WriteRequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new WriteRequestInput( '', [ ] );
		$writeRequest = new WriteRequest( $requestInfo, $requestInput );

		$event = new WriteRequestWasHandledEvent( $writeRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}
