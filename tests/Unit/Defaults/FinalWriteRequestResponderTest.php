<?php
namespace IceHawk\IceHawk\Tests\Unit\Defaults;

use IceHawk\IceHawk\Defaults\FinalWriteResponder;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Requests\WriteRequest;
use IceHawk\IceHawk\Routing\RouteRequest;

/**
 * Class FinalWriteRequestResponderTest
 * @package IceHawk\IceHawk\Tests\Unit\Defaults
 */
class FinalWriteRequestResponderTest extends \PHPUnit_Framework_TestCase
{
	public function testHandleUncaughtException()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_read',
			]
		);
		
		$routeRequest = new RouteRequest( $requestInfo->getUri(), $requestInfo->getMethod() );
		$requestData  = new WriteRequest( $requestInfo, [ ], '' );

		try
		{
			$unresolvedRequest = ( new UnresolvedRequest() )->withDestinationInfo( $routeRequest );

			$responder = new FinalWriteResponder();
			$responder->handleUncaughtException( $unresolvedRequest, $requestData );

			$this->fail( 'No Exception thrown' );
		}
		catch ( UnresolvedRequest $ex )
		{
			$this->assertSame( $routeRequest, $ex->getDestinationInfo() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown' );
		}
	}
}