<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\FinalWriteRequestResponder;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;

/**
 * Class FinalWriteRequestResponderTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Defaults
 */
class FinalWriteRequestResponderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testHandleNoResponse()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/domain/ice_hawk_write',
			]
		);

		$requestData = new WriteRequest( $requestInfo, new WriteRequestInput( '', [ ] ) );

		$responder = new FinalWriteRequestResponder();
		$responder->handleNoResponse( $requestData );

		$this->expectOutputString(
			sprintf(
				"Application did not respond to the %s request: %s", $requestInfo->getMethod(), $requestInfo->getUri()
			)
		);
	}

	public function testHandleUncaughtException()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_read',
			]
		);

		$requestData = new WriteRequest( $requestInfo, new WriteRequestInput( '', [ ] ) );

		try
		{
			$unresolvedRequest = ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
			
			$responder = new FinalWriteRequestResponder();
			$responder->handleUncaughtException( $unresolvedRequest, $requestData );

			$this->fail( 'No Exception thrown' );
		}
		catch ( UnresolvedRequest $ex )
		{
			$this->assertSame( $requestInfo, $ex->getRequestInfo() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown' );
		}
	}
}