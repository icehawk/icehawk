<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestQuery;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$getData    = [ 'testValue' => 'Unit-Test' ];
		$getRequest = new ReadRequest( RequestInfo::fromEnv(), new ReadRequestInput( $getData ) );

		$query = new TestQuery( $getRequest );

		$this->assertEquals( 'Unit-Test', $query->getTestValue() );
		$this->assertEquals( $getData, $query->getTestData() );
	}

	public function testCanAccessRequestInfo()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/valid_read_test',
			]
		);

		$getRequest = new ReadRequest( $requestInfo, new ReadRequestInput( [ ] ) );

		$query = new TestQuery( $getRequest );

		$this->assertEquals( $requestInfo, $query->getRequestInfo() );
	}
}
