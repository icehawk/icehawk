<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\AbstractUriRewriter;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class UriRewriter
 * @package Fortuneglobe\IceHawk
 */
class UriRewriter extends AbstractUriRewriter
{
	public function rewrite( ProvidesRequestInfo $requestInfo ) : Redirect
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), [ ] );
	}
}