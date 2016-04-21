<?php
namespace Fortuneglobe\IceHawk\RequestParsers;

use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;

/**
 * Class NullParser
 *
 * @package Fortuneglobe\IceHawk\RequestParsers
 */
class NullParser implements ParsesRequestBody
{
	public function parse( string $body ) : array
	{
		return [];
	}
}