<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\AbstractUriRewriter;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class UriRewriter
 * @package Fortuneglobe\IceHawk
 */
class UriRewriter extends AbstractUriRewriter
{
	public function rewrite( ServesRequestInfo $requestInfo ) : Redirect
	{
		return $this->rewriteUriBySimpleMap( $requestInfo->getUri(), [ ] );
	}
}