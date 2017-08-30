<?php declare(strict_types=1);
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

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\PubSub\EventPublisher;
use IceHawk\IceHawk\RequestHandlers\OptionsRequestHandler;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use PHPUnit\Framework\TestCase;

class OptionsRequestHandlerTest extends TestCase
{
	/**
	 * @dataProvider RouteProvider
	 * @runInSeparateProcess
	 *
	 * @param array  $readRoutes
	 * @param array  $writeRoutes
	 * @param string $uri
	 * @param array  $expectedMethods
	 */
	public function testHeaderOutput( array $readRoutes, array $writeRoutes, string $uri, array $expectedMethods )
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'OPTIONS',
				'REQUEST_URI'    => $uri,
			]
		);

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->method( 'getWriteRoutes' )->willReturn( $writeRoutes );
		$config->method( 'getReadRoutes' )->willReturn( $readRoutes );

		$optionsRequestHandler = new OptionsRequestHandler( $requestInfo, $config, new EventPublisher() );
		$optionsRequestHandler->handleRequest();

		$expectedHeader = 'Allow: ' . implode( ',', $expectedMethods );

		$this->assertContains( $expectedHeader, xdebug_get_headers() );
	}

	public function RouteProvider()
	{
		return [
			[
				[
					new ReadRoute( new Literal( '/get/this' ), new GetRequestHandler() ),
					new ReadRoute( new Literal( '/get/that' ), new HeadRequestHandler() ),
					new ReadRoute( new Literal( '/get/this/again' ), new GetRequestHandler() ),
				],
				[
					new WriteRoute( new Literal( '/do/this' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/do/this/again' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/do/that' ), new PutRequestHandler() ),
					new WriteRoute( new Literal( '/do/whatever/you/want' ), new DeleteRequestHandler() ),
				],
				'/do/this',
				[ 'POST' ],
			],
			[
				[
					new ReadRoute( new Literal( '/this' ), new GetRequestHandler() ),
					new ReadRoute( new Literal( '/this' ), new HeadRequestHandler() ),
					new ReadRoute( new Literal( '/get/this/again' ), new GetRequestHandler() ),
				],
				[
					new WriteRoute( new Literal( '/this' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/do/this/again' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/this' ), new PutRequestHandler() ),
					new WriteRoute( new Literal( '/this' ), new DeleteRequestHandler() ),
				],
				'/this',
				[ 'HEAD', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE' ],
			],
		];
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testHeaderOutputWithGeneratedRoutes()
	{
		$routes = [
			new ReadRoute( new Literal( '/get/this' ), new GetRequestHandler() ),
			new ReadRoute( new Literal( '/get/that' ), new HeadRequestHandler() ),
			new ReadRoute( new Literal( '/get/this/again' ), new GetRequestHandler() ),
		];

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'OPTIONS',
				'REQUEST_URI'    => '/get/that',
			]
		);

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->method( 'getWriteRoutes' )->willReturn( [] );
		$config->method( 'getReadRoutes' )->willReturn( $this->getGeneratedRoutes( $routes ) );

		$optionsRequestHandler = new OptionsRequestHandler( $requestInfo, $config, new EventPublisher() );
		$optionsRequestHandler->handleRequest();

		$this->assertContains( 'Allow: HEAD', xdebug_get_headers() );
	}

	public function getGeneratedRoutes( array $routes )
	{
		foreach ( $routes as $route )
		{
			yield $route;
		}
	}
}
