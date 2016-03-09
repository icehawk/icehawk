<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\DomainRequestHandlers;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestPostRequestHandler;

class PostRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestType
	 */
	public function testInjectingAGetRequestThrowsException()
	{
		$getRequest = new ReadRequest( RequestInfo::fromEnv(), [ ] );

		new TestPostRequestHandler( $getRequest );
	}

	public function testCanHandleValidRequest()
	{
		$postRequest = new WriteRequest( RequestInfo::fromEnv(), [ ], [ ] );

		$handler = new TestPostRequestHandler( $postRequest );
		$handler->handleRequest();

		$this->expectOutputString( 'Request handled.' );
	}
}
