<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;

/**
 * Class TestDeleteRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestDeleteRequestResolver extends AbstractWriteRequestResolver
{
	protected function createDefaultRequestHandler() : HandlesWriteRequest
	{
		return new DeleteRequestHandler();
	}
}