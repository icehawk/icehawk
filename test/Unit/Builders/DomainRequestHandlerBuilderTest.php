<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Test\Unit\Fixures\Domain\ValidTestRequestHandler;
use Fortuneglobe\IceHawk\UriComponents;

class DomainRequestHandlerBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed
	 */
	public function testBuildingThrowsExceptionIfHandlerDoesNotExist()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder( 'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures' );
		$uriComponents               = new UriComponents( 'not', 'existing' );
		$request                     = new GetRequest( [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests
	 */
	public function testBuildingThrowsExceptionIfHandlerExistsButInterfaceImplementationIsMissing()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder( 'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures' );
		$uriComponents               = new UriComponents( 'domain', 'invalid-test' );
		$request                     = new GetRequest( [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );
	}

	public function testBuildingSucceedsIfHandlerExistsAndImplementsInterface()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder( 'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures' );
		$uriComponents               = new UriComponents( 'domain', 'valid-test' );
		$request                     = new GetRequest( [ ] );

		$handler = $domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );

		$this->assertInstanceOf( ValidTestRequestHandler::class, $handler );
	}
}
