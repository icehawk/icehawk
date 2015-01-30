<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class Forbidden
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class Forbidden extends BaseResponse
{
	public function respond()
	{
		header( Http::FORBIDDEN );
		echo "Forbidden!";
		exit();
	}
}