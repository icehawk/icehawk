<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\ValidReadTestRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidWriteTestRequestHandler;

class DomainRequestHandlerBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed
	 */
	public function testBuildingThrowsExceptionIfHandlerDoesNotExist()
	{
		$uriComponents               = $this->getUriComponentsMock( 'not', 'existing', [ ] );
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			Http::METHOD_GET,
			$uriComponents
		);

		$request = new GetRequest( RequestInfo::fromEnv(), [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $request );
	}

	/**
	 * @param string $domain
	 * @param string $demand
	 * @param array  $params
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject|ServesUriComponents
	 */
	private function getUriComponentsMock( $domain, $demand, array $params )
	{
		$mock = $this->getMockBuilder( ServesUriComponents::class )->setMethods(
			[ 'getDomain', 'getDemand', 'getParams' ]
		)->getMock();

		$mock->expects( $this->once() )->method( 'getDomain' )->willReturn( $domain );
		$mock->expects( $this->once() )->method( 'getDemand' )->willReturn( $demand );
		$mock->expects( $this->never() )->method( 'getParams' )->willReturn( $params );

		return $mock;
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests
	 */
	public function testBuildingThrowsExceptionIfHandlerExistsButInterfaceImplementationIsMissing()
	{
		$uriComponents               = $this->getUriComponentsMock( 'domain', 'invalid-test', [ ] );
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			Http::METHOD_GET,
			$uriComponents
		);

		$request = new GetRequest( RequestInfo::fromEnv(), [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $request );
	}

	public function testBuildingSucceedsIfReadSideHandlerExistsAndImplementsInterface()
	{
		$uriComponents               = $this->getUriComponentsMock( 'domain', 'valid-read-test', [ ] );
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			Http::METHOD_GET,
			$uriComponents
		);

		$request = new GetRequest( RequestInfo::fromEnv(), [ ] );

		$handler = $domainRequestHandlerBuilder->buildDomainRequestHandler( $request );

		$this->assertInstanceOf( ValidReadTestRequestHandler::class, $handler );
	}

	public function testBuildingSucceedsIfWriteSideHandlerExistsAndImplementsInterface()
	{
		$uriComponents               = $this->getUriComponentsMock( 'domain', 'valid-write-test', [ ] );
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			Http::METHOD_POST,
			$uriComponents
		);

		$request = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );

		$handler = $domainRequestHandlerBuilder->buildDomainRequestHandler( $request );

		$this->assertInstanceOf( ValidWriteTestRequestHandler::class, $handler );
	}
}
