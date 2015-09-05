<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUploadedFiles;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\UriComponents;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider invalidRequestMethodProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod
	 */
	public function testBuildingRequestWithInvalidRequestMethodThrowsException( $requestMethod )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents = new UriComponents( 'Unit', 'Test', [ ] );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$requestBuilder->buildRequest( [ ], [ ], [ ] );
	}

	public function invalidRequestMethodProvider()
	{
		return [
			[ 'PUT' ],
			[ 'PATCH' ],
			[ 'DELETE' ],
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
		$uriComponents = new UriComponents( 'Unit', 'Test', [ ] );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->buildRequest( [ ], [ ], [ ] );

		$this->assertInstanceOf( $excpectedClassName, $request );
	}

	public function validRequestMethodsProvider()
	{
		return [
			[ 'GET', GetRequest::class ],
			[ 'GET', ServesGetRequestData::class ],
			[ 'HEAD', GetRequest::class ],
			[ 'HEAD', ServesGetRequestData::class ],
			[ 'POST', PostRequest::class ],
			[ 'POST', ServesPostRequestData::class ],
			[ 'POST', ServesUploadedFiles::class ],
		];
	}

	/**
	 * @dataProvider uriComponentsParamsProvider
	 */
	public function testRequestHasParamsFromUriComponents( $requestMethod, array $params )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new UriComponents( 'Unit', 'Test', $params );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->buildRequest( [ ], [ ], [ ] );

		$this->assertSame( $params, $request->getData() );
	}

	public function uriComponentsParamsProvider()
	{
		return [
			[ 'GET', [ 'unit' => 'test', 'test' => 'unit' ] ],
			[ 'HEAD', [ 'unit' => 'test', 'test' => 'unit' ] ],
			[ 'POST', [ 'unit' => 'test', 'test' => 'unit' ] ],
		];
	}

	/**
	 * @dataProvider uriComponentsParamsOverrideProvider
	 */
	public function testUriComponentsParamsOverrideRequestParams(
		$requestMethod, array $getData, array $postData, array $params, array $expectedData
	)
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new UriComponents( 'Unit', 'Test', $params );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->buildRequest( $getData, $postData, [ ] );

		$this->assertSame( $expectedData, $request->getData() );
	}

	public function uriComponentsParamsOverrideProvider()
	{
		return [
			[
				'GET',
				[ 'unit' => 'unit', 'blubb' => 'bla' ],
				[ ],
				[ 'unit' => 'test', 'test' => 'unit' ],
				[ 'unit' => 'test', 'blubb' => 'bla', 'test' => 'unit' ]
			],
			[
				'HEAD',
				[ 'unit' => 'unit', 'blubb' => 'bla' ],
				[ ],
				[ 'unit' => 'test', 'test' => 'unit' ],
				[ 'unit' => 'test', 'blubb' => 'bla', 'test' => 'unit' ]
			],
			[
				'POST',
				[ ],
				[ 'unit' => 'unit', 'blubb' => 'bla' ],
				[ 'unit' => 'test', 'test' => 'unit' ],
				[ 'unit' => 'test', 'blubb' => 'bla', 'test' => 'unit' ]
			],
		];
	}
}
