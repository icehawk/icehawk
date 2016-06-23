<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class IceHawkReadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class IceHawkReadRequestHandler implements HandlesGetRequest
{	
	public function handle( ProvidesReadRequestData $request )
	{
		echo 'Handler method for get request called.';
	}
}