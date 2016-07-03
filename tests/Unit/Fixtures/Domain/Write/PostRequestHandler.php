<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PostRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class PostRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}