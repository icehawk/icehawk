<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PostRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class PostRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
	}
}