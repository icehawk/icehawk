<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PatchRequestHandler;

/**
 * Class TestPatchRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestPatchRequestResolver extends AbstractWriteRequestResolver
{
	protected function createDefaultRequestHandler() : HandlesWriteRequest
	{
		return new PatchRequestHandler();
	}
}