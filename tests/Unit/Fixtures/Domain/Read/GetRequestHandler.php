<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class GetRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class GetRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}