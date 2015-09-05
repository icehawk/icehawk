<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\DomainRequestHandlers\PostRequestHandler as BasePostRequestHandler;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;

/**
 * Class PostRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class PostRequestHandler extends BasePostRequestHandler
{
	public function handle( ServesPostRequestData $request )
	{
		echo "Request handled.";
	}
}
