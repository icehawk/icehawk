<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use IceHawk\IceHawk\Interfaces\HandlesDeleteRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class DeleteRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class DeleteRequestHandler implements HandlesDeleteRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}