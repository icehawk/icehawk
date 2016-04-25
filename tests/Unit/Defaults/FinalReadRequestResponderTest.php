<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\FinalReadRequestResponder;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;

/**
 * Class FinalReadRequestResponderTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Defaults
 */
class FinalReadRequestResponderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testHandleNoResponse()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_read',
			]
		);
		
		$requestData = new ReadRequest( $requestInfo, new ReadRequestInput( [] ) );
		
		$responder = new FinalReadRequestResponder();		
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
		
		$requestData = new ReadRequest( $requestInfo, new ReadRequestInput( [] ) );

		try
		{
			$unresolvedRequest = ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
		
			$responder = new FinalReadRequestResponder();
			$responder->handleUncaughtException( $unresolvedRequest, $requestData );
			
			$this->fail('No Exception thrown');
		}
		catch ( UnresolvedRequest $ex )
		{
			$this->assertSame( $requestInfo, $ex->getRequestInfo() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown');	
		}
	}
}