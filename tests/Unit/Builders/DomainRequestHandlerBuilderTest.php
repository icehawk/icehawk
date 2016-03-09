<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\RequestHandlerBuilder;
use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Interfaces\ProvidesHandlerDemand;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
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
		$domainRequestHandlerBuilder = new RequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			HttpMethod::GET,
			$uriComponents
		);

		$request = new ReadRequest( RequestInfo::fromEnv(), [ ] );

		$domainRequestHandlerBuilder->build( $request );
	}

	/**
	 * @param string $domain
	 * @param string $demand
	 * @param array  $params
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject|ProvidesHandlerDemand
	 */
	private function getUriComponentsMock( $domain, $demand, array $params )
	{
		$mock = $this->getMockBuilder( ProvidesHandlerDemand::class )->setMethods(
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
		$domainRequestHandlerBuilder = new RequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			HttpMethod::GET,
			$uriComponents
		);

		$request = new ReadRequest( RequestInfo::fromEnv(), [ ] );

		$domainRequestHandlerBuilder->build( $request );
	}

	public function testBuildingSucceedsIfReadSideHandlerExistsAndImplementsInterface()
	{
		$uriComponents               = $this->getUriComponentsMock( 'domain', 'valid-read-test', [ ] );
		$domainRequestHandlerBuilder = new RequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			HttpMethod::GET,
			$uriComponents
		);

		$request = new ReadRequest( RequestInfo::fromEnv(), [ ] );

		$handler = $domainRequestHandlerBuilder->build( $request );

		$this->assertInstanceOf( ValidReadTestRequestHandler::class, $handler );
	}

	public function testBuildingSucceedsIfWriteSideHandlerExistsAndImplementsInterface()
	{
		$uriComponents               = $this->getUriComponentsMock( 'domain', 'valid-write-test', [ ] );
		$domainRequestHandlerBuilder = new RequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures',
			HttpMethod::POST,
			$uriComponents
		);

		$request = new WriteRequest( RequestInfo::fromEnv(), [ ], [ ] );

		$handler = $domainRequestHandlerBuilder->build( $request );

		$this->assertInstanceOf( ValidWriteTestRequestHandler::class, $handler );
	}
}
