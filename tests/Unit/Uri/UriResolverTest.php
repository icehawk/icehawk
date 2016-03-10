<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Uri;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\UriResolver;

class UriResolverTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider malformedUriProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testMalformedUriThrowsException( $uri )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$resolver = new UriResolver();
		$resolver->resolveUri( $requestInfo );
	}

	public function malformedUriProvider()
	{
		return [
			[ 'unit-test' ],
			[ '/unit-test' ],
			[ 'unit/test' ],
		];
	}

	/**
	 * @dataProvider validUriProvider
	 */
	public function testValidUriResolvesToUriComponents( $uri, $expectedDomain, $expectedDemand )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$resolver   = new UriResolver();
		$components = $resolver->resolveUri( $requestInfo );

		$this->assertEquals( $expectedDomain, $components->getDomain() );
		$this->assertEquals( $expectedDemand, $components->getDemand() );
	}

	public function validUriProvider()
	{
		return [
			[ '/Unit/Test', 'unit', 'test' ],
			[ '/Unit/Test/', 'unit', 'test' ],
			[ '/unit/test/', 'unit', 'test' ],
			[ '/unit/test/and/more/components', 'unit', 'test' ],
			[ '/un-it/te-st', 'un-it', 'te-st' ],
			[ '/un_IT/TE_st', 'un_it', 'te_st' ],
		];
	}
}
