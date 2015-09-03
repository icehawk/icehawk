<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Queries;

use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Test\Unit\Fixtures\TestQuery;

class DomainQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$getData    = [ 'testValue' => 'Unit-Test' ];
		$getRequest = new GetRequest( $getData );

		$query = new TestQuery( $getRequest );

		$this->assertEquals( 'Unit-Test', $query->getTestValue() );
		$this->assertEquals( $getData, $query->getTestData() );
	}
}
