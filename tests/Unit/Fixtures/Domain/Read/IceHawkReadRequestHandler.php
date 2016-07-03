<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class IceHawkReadRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class IceHawkReadRequestHandler implements HandlesGetRequest
{	
	public function handle( ProvidesReadRequestData $request )
	{
		echo 'Handler method for get request called.';
	}
}