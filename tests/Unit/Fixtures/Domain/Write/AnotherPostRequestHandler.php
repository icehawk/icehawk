<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

class AnotherPostRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{		
	}
}