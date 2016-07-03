<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class BodyParamRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class BodyDataRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
		echo $request->getInputData()->getBody();
	}
}