<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

class AnotherGetRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}