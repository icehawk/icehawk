<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use IceHawk\IceHawk\Interfaces\HandlesPatchRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PatchRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class PatchRequestHandler implements HandlesPatchRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}