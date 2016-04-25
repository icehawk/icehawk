<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\RedirectingEvent;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class RedirectingEventTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Events
 */
class RedirectingEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$uri = '/domain/redirect';

		$redirect    = new Redirect( $uri );
		$requestInfo = RequestInfo::fromEnv();

		$event = new RedirectingEvent( $redirect, $requestInfo );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $redirect, $event->getRedirect() );
	}
}
