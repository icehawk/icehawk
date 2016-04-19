<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\SimpleResponse;

/**
 * Class IceHawkReadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class IceHawkReadRequestHandler implements HandlesGetRequest
{	
	public function handle( ProvidesReadRequestData $request ) : ServesResponse
	{
		return new SimpleResponse( 'Handler method for get request called.' );
	}
}