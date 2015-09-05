<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\DomainRequestHandlers;

use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\PostRequestHandler;

class PostRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestType
	 */
	public function testInjectingAGetRequestThrowsException()
	{
		$getRequest = new GetRequest( [ ] );

		new PostRequestHandler( $getRequest );
	}

	public function testCanHandleValidRequest()
	{
		$postRequest = new PostRequest( [ ], [ ] );

		$handler = new PostRequestHandler( $postRequest );
		$handler->handleRequest();

		$this->expectOutputString( 'Request handled.' );
	}
}
