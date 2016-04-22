<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\WriteHandlerRouter;

/**
 * Class AbstractWriteResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
abstract class AbstractWriteResolver implements ResolvesWriteRequest
{
	/**
	 * @var string
	 */
	protected $defaultRequestRoute;

	public function __construct( string $defaultRequestRoute )
	{
		$this->defaultRequestRoute = $defaultRequestRoute;
	}

	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler
	{
		if( $requestInfo->getUri() == $this->defaultRequestRoute  )
		{
			return new WriteHandlerRouter( $this->createDefaultRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}

	abstract protected function createDefaultRequestHandler() : HandlesWriteRequest;
}