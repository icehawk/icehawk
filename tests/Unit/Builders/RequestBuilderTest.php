<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\HandlerRoute;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFiles;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param string $requestMethod
	 *
	 * @dataProvider invalidRequestMethodProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod
	 */
	public function testBuildingRequestWithInvalidRequestMethodThrowsException( $requestMethod )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new HandlerRoute( 'Unit', 'Test', [ ] );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$requestBuilder->build( [ ], [ ], [ ] );
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
	 *
	 * @param string $requestMethod
	 * @param string $excpectedClassName
	 *
	 * @dataProvider validRequestMethodsProvider
	 */
	public function testBuildsRequestInstanceWithValidRequestMethod( $requestMethod, $excpectedClassName )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new HandlerRoute( 'Unit', 'Test', [ ] );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->build( [ ], [ ], [ ] );

		$this->assertInstanceOf( $excpectedClassName, $request );
	}

	public function validRequestMethodsProvider()
	{
		return [
			[ 'GET', ReadRequest::class ],
			[ 'GET', ProvidesReadRequestData::class ],
			[ 'HEAD', ReadRequest::class ],
			[ 'HEAD', ProvidesReadRequestData::class ],
			[ 'POST', WriteRequest::class ],
			[ 'POST', ProvidesWriteRequestData::class ],
			[ 'POST', ProvidesUploadedFiles::class ],
		];
	}

	/**
	 * @dataProvider uriComponentsParamsProvider
	 */
	public function testRequestHasParamsFromUriComponents( $requestMethod, array $params )
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new HandlerRoute( 'Unit', 'Test', $params );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->build( [ ], [ ], [ ] );

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
	 *
	 * @param string $requestMethod
	 * @param array  $getData
	 * @param array  $postData
	 * @param array  $params
	 * @param array  $expectedData
	 *
	 * @throws \Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod
	 * @dataProvider uriComponentsParamsOverrideProvider
	 */
	public function testUriComponentsParamsOverrideRequestParams(
		$requestMethod, array $getData, array $postData, array $params, array $expectedData
	)
	{
		$requestInfo    = new RequestInfo( [ 'REQUEST_METHOD' => $requestMethod ] );
		$uriComponents  = new HandlerRoute( 'Unit', 'Test', $params );
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $requestBuilder->build( $getData, $postData, [ ] );

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
