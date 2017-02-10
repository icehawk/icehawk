<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Routing\Patterns\NamedRegExp;
use IceHawk\IceHawk\Routing\RequestBypass;
use IceHawk\IceHawk\Routing\RequestBypasser;

/**
 * Class RequestBypasserTest
 * @package IceHawk\IceHawk\Tests\Unit\Routing
 */
class RequestBypasserTest extends \PHPUnit\Framework\TestCase
{
	public function setUp()
	{
		$_GET  = [];
		$_POST = [];
	}

	/**
	 * @param array               $requestBypasses
	 * @param ProvidesRequestInfo $requestInfo
	 * @param array               $postData
	 * @param string              $expectedFinalUri
	 * @param string              $expectedFinalMethod
	 * @param string              $expectedQueryString
	 *
	 * @dataProvider writeRequestBypassesProvider
	 */
	public function testCanBypassWriteRoutes(
		array $requestBypasses,
		ProvidesRequestInfo $requestInfo,
		array $postData,
		string $expectedFinalUri,
		string $expectedFinalMethod,
		string $expectedQueryString
	)
	{
		$requestBypasser = new RequestBypasser();

		foreach ( $requestBypasses as $bypassRoute )
		{
			$requestBypasser->addRequestBypass( $bypassRoute );
		}

		$_POST       = $postData;
		$requestInfo = $requestBypasser->bypassRequest( $requestInfo );

		$this->assertEquals( $expectedFinalUri, $requestInfo->getUri() );
		$this->assertEquals( $expectedFinalMethod, $requestInfo->getMethod() );
		$this->assertEquals( $expectedQueryString, $requestInfo->getQueryString() );
		$this->assertEquals( $postData, $_GET );
	}

	public function writeRequestBypassesProvider() : array
	{
		return [
			[
				[
					new RequestBypass(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/stocks/store/:storeId',
						HttpMethod::GET
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::POST,
						'REQUEST_URI'    => '/companies/1/stores/2/stocks',
					]
				),
				[ 'stock' => '3' ],
				'/stocks/store/2',
				HttpMethod::GET,
				'companyId=1&storeId=2',
			],
			[
				[
					new RequestBypass(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::HEAD
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/stocks/store/:storeId',
						HttpMethod::GET
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::HEAD,
						'REQUEST_URI'    => '/companies/1/stores/2/stocks',
						'QUERY_STRING'   => 'stock=1',
					]
				),
				[],
				'/stocks/store/2',
				HttpMethod::GET,
				'stock=1&companyId=1&storeId=2',
			],
		];
	}

	/**
	 * @param array               $requestBypasses
	 * @param ProvidesRequestInfo $request
	 * @param string              $expectedFinalUri
	 * @param string              $expectedFinalMethod
	 * @param array               $expectedPostData
	 *
	 * @dataProvider readRequestBypassesProvider
	 */
	public function testCanBypassReadRoutes(
		array $requestBypasses,
		ProvidesRequestInfo $request,
		string $expectedFinalUri,
		string $expectedFinalMethod,
		array $expectedPostData
	)
	{
		$requestBypasser = new RequestBypasser();

		foreach ( $requestBypasses as $bypassRoute )
		{
			$requestBypasser->addRequestBypass( $bypassRoute );
		}

		$request = $requestBypasser->bypassRequest( $request );

		$this->assertEquals( $expectedFinalUri, $request->getUri() );
		$this->assertEquals( $expectedFinalMethod, $request->getMethod() );
		$this->assertEquals( $expectedPostData, $_POST );
	}

	public function readRequestBypassesProvider() : array
	{
		return [
			[
				[
					new RequestBypass(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::GET
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/company/:companyId/stocks/store/:storeId',
						HttpMethod::POST
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::GET
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::GET,
						'REQUEST_URI'    => '/companies/1/stores/2/stocks',
					]
				),
				'/company/1/stocks/store/2',
				HttpMethod::POST,
				[ 'companyId' => '1', 'storeId' => '2' ],
			],
			[
				[
					new RequestBypass(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::DELETE
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/company/:companyId/stocks/store/:storeId',
						HttpMethod::PUT
					),
					new RequestBypass(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::POST
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::POST,
						'REQUEST_URI'    => '/companies/1/stores/2/stocks',
					]
				),
				'/company/1/stocks/store/2',
				HttpMethod::PUT,
				[ 'companyId' => '1', 'storeId' => '2' ],
			],
		];
	}
}
