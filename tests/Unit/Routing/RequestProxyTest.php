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
use IceHawk\IceHawk\Routing\RequestProxy;
use IceHawk\IceHawk\Routing\RouteRedirect;

/**
 * Class RouteRedirectTest
 * @package IceHawk\IceHawk\Tests\Unit\Routing
 */
class RequestProxyTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeWriteRedirectDataProvider
	 */
	public function testProxyWriteRoutes(
		array $routeRedirects,
		ProvidesRequestInfo $requestInfo,
		array $postData,
		string $expectedFinalUri,
		string $expectedFinalMethod,
		string $expectedQueryString
	)
	{
		$requestProxy = new RequestProxy();

		foreach ( $routeRedirects as $routeRedirect )
		{
			$requestProxy->addRedirect( $routeRedirect );
		}

		$_POST   = $postData;
		$requestInfo = $requestProxy->proxyRequest( $requestInfo );

		$this->assertEquals( $expectedFinalUri, $requestInfo->getUri() );
		$this->assertEquals( $expectedFinalMethod, $requestInfo->getMethod() );
		$this->assertEquals( $expectedQueryString, $requestInfo->getQueryString() );
		$this->assertEquals( $postData, $_GET );
	}

	public function routeWriteRedirectDataProvider()
	{
		return [
			[
				[
					new RouteRedirect(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/stocks/store/:storeId',
						HttpMethod::GET
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
				],
				new RequestInfo(
					[ 'REQUEST_METHOD' => HttpMethod::POST, 'REQUEST_URI' => '/companies/1/stores/2/stocks' ]
				),
				[ 'stock' => '3' ],
				'/stocks/store/2',
				HttpMethod::GET,
				'companyId=1&storeId=2',
			],
			[
				[
					new RouteRedirect(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::HEAD
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/stocks/store/:storeId',
						HttpMethod::GET
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::PUT
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::HEAD, 'REQUEST_URI' => '/companies/1/stores/2/stocks',
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
	 * @dataProvider routeReadRedirectDataProvider
	 */
	public function testProxyReadRoutes(
		array $routeRedirects, ProvidesRequestInfo $request, string $expectedFinalUri, string $expectedFinalMethod,
		array $expectedPostData
	)
	{
		$requestProxy = new RequestProxy();

		foreach ( $routeRedirects as $routeRedirect )
		{
			$requestProxy->addRedirect( $routeRedirect );
		}

		$request = $requestProxy->proxyRequest( $request );

		$this->assertEquals( $expectedFinalUri, $request->getUri() );
		$this->assertEquals( $expectedFinalMethod, $request->getMethod() );
		$this->assertEquals( $expectedPostData, $_POST );
	}

	public function routeReadRedirectDataProvider()
	{
		return [
			[
				[
					new RouteRedirect(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::GET
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/company/:companyId/stocks/store/:storeId',
						HttpMethod::POST
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::GET
					),
				],
				new RequestInfo(
					[ 'REQUEST_METHOD' => HttpMethod::GET, 'REQUEST_URI' => '/companies/1/stores/2/stocks' ]
				),
				'/company/1/stocks/store/2',
				HttpMethod::POST,
				[ 'companyId' => '1', 'storeId' => '2' ],
			],
			[
				[
					new RouteRedirect(
						new NamedRegExp( '^/companies/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::DELETE
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/(?<companyId>\d*)/stores/(?<storeId>\d*)/stocks$' ),
						'/company/:companyId/stocks/store/:storeId',
						HttpMethod::PUT
					),
					new RouteRedirect(
						new NamedRegExp( '^/companies/stores/(?<storeId>\d*)/(?<companyId>\d*)/stocks$' ),
						'/stocks/company/:companyId',
						HttpMethod::POST
					),
				],
				new RequestInfo(
					[
						'REQUEST_METHOD' => HttpMethod::POST, 'REQUEST_URI' => '/companies/1/stores/2/stocks',
					]
				),
				'/company/1/stocks/store/2',
				HttpMethod::PUT,
				[ 'companyId' => '1', 'storeId' => '2' ],
			],
		];
	}
}
