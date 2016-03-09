<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\UriRewriter;

/**
 * Class TestUriRewriterWithInvalidMap
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestUriRewriterWithInvalidMap extends UriRewriter
{

	private static $simpleMap = [
		"/empty/redirect/data"     => [ ],
		"/non/array/redirect/data" => 'not-an-array',
	];

	public function rewrite( ProvidesRequestInfo $requestInfo )
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), self::$simpleMap );
	}
}
