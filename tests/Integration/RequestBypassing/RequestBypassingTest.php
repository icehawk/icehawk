<?php declare(strict_types = 1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Integration\RequestBypassing;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Defaults\IceHawkConfig;
use IceHawk\IceHawk\Defaults\IceHawkDelegate;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\RequestBypass;
use IceHawk\IceHawk\Routing\WriteRoute;

/**
 * Class RequestBypassingTest
 * @package IceHawk\IceHawk\Tests\Integration\RequestBypassing
 */
class RequestBypassingTest extends \PHPUnit\Framework\TestCase
{
	public function testCanBypassRequestToWriteHandler()
	{
		$postHandler = new class implements HandlesPostRequest
		{
			public function handle( ProvidesWriteRequestData $request )
			{
				$requestInfo = $request->getInfo();
				echo "bypassed to " . $requestInfo->getMethod();
			}
		};

		$delegate = new IceHawkDelegate();
		$config   = new class($postHandler) extends IceHawkConfig
		{
			/** @var HandlesPostRequest */
			private $postHandler;

			public function __construct( HandlesPostRequest $postHandler )
			{
				$this->postHandler = $postHandler;
			}

			public function getRequestInfo() : ProvidesRequestInfo
			{
				return new RequestInfo(
					[
						'REQUEST_URI'    => '/get/requested',
						'REQUEST_METHOD' => 'GET',
					]
				);
			}

			public function getRequestBypasses()
			{
				return [
					new RequestBypass(
						new Literal( '/get/requested' ),
						'/bypassed/to/post',
						HttpMethod::POST
					),
				];
			}

			public function getWriteRoutes()
			{
				return [
					new WriteRoute( new Literal( '/bypassed/to/post' ), $this->postHandler ),
				];
			}
		};

		$iceHawk = new IceHawk( new $config( new $postHandler ), $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'bypassed to POST' );
	}

	public function testCanBypassRequestToReadHandler()
	{
		$getHandler = new class implements HandlesGetRequest
		{
			public function handle( ProvidesReadRequestData $request )
			{
				$requestInfo = $request->getInfo();
				echo "bypassed to " . $requestInfo->getMethod();
			}
		};

		$delegate = new IceHawkDelegate();
		$config   = new class($getHandler) extends IceHawkConfig
		{
			/** @var HandlesGetRequest */
			private $getHandler;

			public function __construct( HandlesGetRequest $getHandler )
			{
				$this->getHandler = $getHandler;
			}

			public function getRequestInfo() : ProvidesRequestInfo
			{
				return new RequestInfo(
					[
						'REQUEST_URI'    => '/post/requested',
						'REQUEST_METHOD' => 'POST',
					]
				);
			}

			public function getRequestBypasses()
			{
				return [
					new RequestBypass(
						new Literal( '/post/requested' ),
						'/bypassed/to/get',
						HttpMethod::GET
					),
				];
			}

			public function getReadRoutes()
			{
				return [
					new ReadRoute( new Literal( '/bypassed/to/get' ), $this->getHandler ),
				];
			}
		};

		$iceHawk = new IceHawk( new $config( new $getHandler ), $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'bypassed to GET' );
	}
}
