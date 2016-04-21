<?php
namespace Fortuneglobe\IceHawk\RequestParsers;

use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;

/**
 * Class FormParser
 *
 * @package Fortuneglobe\IceHawk\RequestParsers
 */
class FormBodyParser implements ParsesRequestBody
{
	public function parse( string $body ) : array
	{
		parse_str( $body, $params );

		return $params;
	}
}