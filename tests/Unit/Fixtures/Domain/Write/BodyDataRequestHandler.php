<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class BodyParamRequestHandler
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class BodyDataRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request )
	{
		echo $request->getInputData()->getBody();
	}
}
