<?php
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

class OptionsRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider RouteProvider
	 * @runInSeparateProcess
	 */
	public function testHeaderOutput( array $readRoutes, array $writeRoutes, string $uri, array $expectedMethods)
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

		$optionsRequestHandler = new OptionsRequestHandler( $config, new EventPublisher() );
		$optionsRequestHandler->handleRequest();

		$expectedHeader = 'Allow: ' . join( ',', $expectedMethods );
		
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
}
