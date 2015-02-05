<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class NotFound
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class NotFound extends BaseResponse
{
	public function respond()
	{
		header( Http::NOT_FOUND );
		exit();
	}
}