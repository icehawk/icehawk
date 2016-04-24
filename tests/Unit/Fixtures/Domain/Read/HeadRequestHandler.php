<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesHeadRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\SimpleResponse;

/**
 * Class HeadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class HeadRequestHandler implements HandlesHeadRequest
{
	public function handle( ProvidesReadRequestData $request ) : ServesResponse
	{
		return new SimpleResponse();
	}
}