<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Queries;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestQuery;

class DomainQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$getData    = [ 'testValue' => 'Unit-Test' ];
		$getRequest = new ReadRequest( RequestInfo::fromEnv(), $getData );

		$query = new TestQuery( $getRequest );

		$this->assertEquals( 'Unit-Test', $query->getTestValue() );
		$this->assertEquals( $getData, $query->getTestData() );
	}

	public function testCanAccessRequestInfoFromRequest()
	{
		$requestInfo = RequestInfo::fromEnv();
		$getRequest  = new ReadRequest( $requestInfo, [ ] );

		$query = new TestQuery( $getRequest );

		$this->assertSame( $requestInfo, $query->getRequestInfo() );
	}
}
