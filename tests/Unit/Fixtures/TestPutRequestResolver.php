<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;

/**
 * Class TestPutRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestPutRequestResolver extends AbstractWriteResolver
{
	protected function createDefaultRequestHandler() : HandlesWriteRequest
	{
		return new PutRequestHandler();
	}
}