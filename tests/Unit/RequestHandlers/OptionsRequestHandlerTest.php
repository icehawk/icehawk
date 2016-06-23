<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\RequestHandlers;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\RequestHandlers\OptionsRequestHandler;
use Fortuneglobe\IceHawk\Routing\Patterns\Literal;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;

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
				[ 'GET', 'HEAD', 'POST', 'PUT', 'DELETE' ],
			],
		];
	}
}
