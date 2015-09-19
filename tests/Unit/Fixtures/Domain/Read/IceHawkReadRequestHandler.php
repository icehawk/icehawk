<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read;

use Fortuneglobe\IceHawk\DomainRequestHandlers\GetRequestHandler;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;

/**
 * Class IceHawkReadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read
 */
class IceHawkReadRequestHandler extends GetRequestHandler
{
	public function handle( ServesGetRequestData $request )
	{
		echo "Handler method for get request called.";
	}
}