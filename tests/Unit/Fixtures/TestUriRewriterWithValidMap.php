<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class TestUriRewriterWithValidMap
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestUriRewriterWithValidMap extends UriRewriter
{

	private static $simpleMap = [
		"#/non/regex/rewrite#"      => [ '/non_regex_rewrite', HttpCode::MOVED_PERMANENTLY ],
		"#/non/regex/no/code#"      => [ '/non_regex_no_code' ],
		"#^/regex/rewrite/?#"       => [ '/regex_rewrite', HttpCode::FOUND ],
		"#^/regex/param/([^/]+)/?#" => [ '/regex_param_$1', HttpCode::FOUND ],
	];

	public function rewrite( ProvidesRequestInfo $requestInfo )
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), self::$simpleMap );
	}
}
