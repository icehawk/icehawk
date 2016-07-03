<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

class AnotherPostRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{		
	}
}