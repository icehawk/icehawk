<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use IceHawk\IceHawk\Interfaces\HandlesHeadRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class HeadRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class HeadRequestHandler implements HandlesHeadRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}