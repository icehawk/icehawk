<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ResolvesUri
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ResolvesUri
{
	public function resolveUri( ServesRequestInfo $requestInfo ) : ServesUriComponents;
}