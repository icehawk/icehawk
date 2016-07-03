<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\ReadRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;

/**
 * Class ReadRequestWasHandledEventTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Events
 */
class ReadRequestWasHandledEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo  = RequestInfo::fromEnv();
		$requestInput = new ReadRequestInput( [ ] );
		$readRequest = new ReadRequest( $requestInfo, $requestInput );

		$event = new ReadRequestWasHandledEvent( $readRequest );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestInput, $event->getInputData() );
	}
}