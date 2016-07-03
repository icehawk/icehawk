<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\FinalWriteResponder;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;
use Fortuneglobe\IceHawk\Routing\RouteRequest;

/**
 * Class FinalWriteRequestResponderTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Defaults
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
		$requestData  = new WriteRequest( $requestInfo, new WriteRequestInput( '', [ ] ) );

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