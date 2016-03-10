<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;

/**
 * Trait DefaultUriRewriting
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultUriRewriting
{
	public function getUriRewriter() : RewritesUri
	{
		return new UriRewriter();
	}
}