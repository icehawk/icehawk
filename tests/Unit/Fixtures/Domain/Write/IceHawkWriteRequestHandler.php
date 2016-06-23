<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class IceHawkWriteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class IceHawkWriteRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
		echo 'Handler method for post request called.';
	}
}