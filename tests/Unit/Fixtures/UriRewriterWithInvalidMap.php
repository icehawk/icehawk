<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\UriRewriter;

/**
 * Class UriRewriterWithInvalidMap
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class UriRewriterWithInvalidMap extends UriRewriter
{

	private static $simpleMap = [
		"/empty/redirect/data"     => [ ],
		"/non/array/redirect/data" => 'not-an-array',
	];

	public function rewrite( ServesRequestInfo $requestInfo )
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), self::$simpleMap );
	}
}
