<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Class ValidReadTestRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain
 */
class ValidGetRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
	}
}
