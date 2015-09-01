<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\DomainRequestHandlers;

use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Test\Unit\Fixtures\GetRequestHandler;

class GetRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestType
	 */
	public function testInjectingAPostRequestThrowsException()
	{
		$postRequest = new PostRequest( [ ], [ ] );

		new GetRequestHandler( $postRequest );
	}

	public function testCanHandleValidRequest()
	{
		$getRequest = new GetRequest( [ ] );

		$handler = new GetRequestHandler( $getRequest );
		$handler->handleRequest();

		$this->expectOutputString( 'Request handled.' );
	}
}
