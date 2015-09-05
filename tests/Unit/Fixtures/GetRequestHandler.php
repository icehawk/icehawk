<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\DomainRequestHandlers\GetRequestHandler as BaseGetRequestHandler;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;

/**
 * Class GetRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class GetRequestHandler extends BaseGetRequestHandler
{
	public function handle( ServesGetRequestData $request )
	{
		echo "Request handled.";
	}
}
