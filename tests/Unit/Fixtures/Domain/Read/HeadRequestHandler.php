<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesHeadRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class HeadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class HeadRequestHandler implements HandlesHeadRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}