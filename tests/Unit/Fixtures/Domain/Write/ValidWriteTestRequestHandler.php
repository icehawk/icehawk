<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;

/**
 * Class ValidWriteTestRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class ValidWriteTestRequestHandler implements HandlesPostRequest
{
	public function handle( ProvidesWriteRequestData $request ) : ServesResponse
	{
	}
}
