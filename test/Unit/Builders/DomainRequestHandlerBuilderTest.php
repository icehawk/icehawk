<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Builders;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Test\Unit\Fixures\Domain\Read\ValidReadTestRequestHandler;
use Fortuneglobe\IceHawk\Test\Unit\Fixures\Domain\Write\ValidWriteTestRequestHandler;
use Fortuneglobe\IceHawk\UriComponents;

class DomainRequestHandlerBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed
	 */
	public function testBuildingThrowsExceptionIfHandlerDoesNotExist()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures',
			Http::METHOD_GET
		);
		$uriComponents               = new UriComponents( 'not', 'existing' );
		$request                     = new GetRequest( [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests
	 */
	public function testBuildingThrowsExceptionIfHandlerExistsButInterfaceImplementationIsMissing()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures',
			Http::METHOD_GET
		);
		$uriComponents               = new UriComponents( 'domain', 'invalid-test' );
		$request                     = new GetRequest( [ ] );

		$domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );
	}

	public function testBuildingSucceedsIfReadSideHandlerExistsAndImplementsInterface()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures',
			Http::METHOD_GET
		);
		$uriComponents               = new UriComponents( 'domain', 'valid-read-test' );
		$request                     = new GetRequest( [ ] );

		$handler = $domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );

		$this->assertInstanceOf( ValidReadTestRequestHandler::class, $handler );
	}

	public function testBuildingSucceedsIfWriteSideHandlerExistsAndImplementsInterface()
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			'Fortuneglobe\\IceHawk\\Test\\Unit\\Fixures',
			Http::METHOD_POST
		);
		$uriComponents               = new UriComponents( 'domain', 'valid-write-test' );
		$request                     = new PostRequest( [ ], [ ] );

		$handler = $domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );

		$this->assertInstanceOf( ValidWriteTestRequestHandler::class, $handler );
	}
}
