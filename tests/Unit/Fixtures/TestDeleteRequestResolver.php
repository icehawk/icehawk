<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;

/**
 * Class TestPostRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestDeleteRequestResolver extends AbstractWriteResolver
{
	protected function createDefaultRequestHandler() : HandlesWriteRequest
	{
		return new DeleteRequestHandler();
	}
}