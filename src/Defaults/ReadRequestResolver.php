<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;

/**
 * Class ReadRequestResolver
 * @package Fortuneglobe\IceHawk
 */
class ReadRequestResolver implements ResolvesReadRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToReadHandler
	{
		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}