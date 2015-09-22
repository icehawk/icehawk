<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Queries;

use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestDomainQuery;

class DomainQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$getData    = [ 'testValue' => 'Unit-Test' ];
		$getRequest = new GetRequest( $getData );

		$query = new TestDomainQuery( $getRequest );

		$this->assertEquals( 'Unit-Test', $query->getTestValue() );
		$this->assertEquals( $getData, $query->getTestData() );
	}
}
