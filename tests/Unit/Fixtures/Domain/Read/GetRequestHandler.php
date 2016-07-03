<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class GetRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class GetRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}