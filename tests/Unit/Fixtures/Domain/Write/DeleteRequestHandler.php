<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesDeleteRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class DeleteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class DeleteRequestHandler implements HandlesDeleteRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}