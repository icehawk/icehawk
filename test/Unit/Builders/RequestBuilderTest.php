<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUploadedFiles;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider invalidRequestMethodProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod
	 */
	public function testBuildingRequestWithInvalidRequestMethodThrowsException( $requestMethod )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$requestBuilder = new RequestBuilder( $requestInfo );

		$requestBuilder->buildRequest( [ ], [ ], [ ] );
	}

	public function invalidRequestMethodProvider()
	{
		return [
			[ 'PUT' ],
			[ 'PATCH' ],
			[ 'DELETE' ],
			[ 'HEAD' ],
			[ 'OPTIONS' ],
			[ 'TRACE' ],
			[ 'CONNECT' ],
		];
	}

	/**
	 * @dataProvider validRequestMethodsProvider
	 */
	public function testBuildsRequestInstanceWithValidRequestMethod( $requestMethod, $excpectedClassName )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$requestBuilder = new RequestBuilder( $requestInfo );

		$request = $requestBuilder->buildRequest( [ ], [ ], [ ] );

		$this->assertInstanceOf( $excpectedClassName, $request );
	}

	public function validRequestMethodsProvider()
	{
		return [
			[ 'GET', GetRequest::class ],
			[ 'GET', ServesGetRequestData::class ],
			[ 'POST', PostRequest::class ],
			[ 'POST', ServesPostRequestData::class ],
			[ 'POST', ServesUploadedFiles::class ],
		];
	}
}
