<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Interface RewritesUri
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RewritesUri
{
	public function rewrite( ServesRequestInfo $requestInfo ) : Redirect;
}