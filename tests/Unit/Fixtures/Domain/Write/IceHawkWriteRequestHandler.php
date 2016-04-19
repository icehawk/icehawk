<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\SimpleResponse;

/**
 * Class IceHawkWriteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class IceHawkWriteRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request ) : ServesResponse
	{
		return new SimpleResponse( 'Handler method for post request called.' );
	}
}