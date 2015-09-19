<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write;

use Fortuneglobe\IceHawk\DomainRequestHandlers\PostRequestHandler;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;

/**
 * Class IceHawkWriteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write
 */
class IceHawkWriteRequestHandler extends PostRequestHandler
{
	public function handle( ServesPostRequestData $request )
	{
		echo "Handler method for post request called.";
	}
}