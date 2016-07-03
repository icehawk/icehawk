<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPatchRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PatchRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class PatchRequestHandler implements HandlesPatchRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}