<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\DomainRequestHandlers;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestGetRequestHandler;

class GetRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestType
	 */
	public function testInjectingAPostRequestThrowsException()
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );

		new TestGetRequestHandler( $postRequest );
	}

	public function testCanHandleValidRequest()
	{
		$getRequest = new GetRequest( RequestInfo::fromEnv(), [ ] );

		$handler = new TestGetRequestHandler( $getRequest );
		$handler->handleRequest();

		$this->expectOutputString( 'Request handled.' );
	}
}
