<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesUriComponents
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesUriComponents
{
	public function getDomain() : string;

	public function getDemand() : string;

	public function getParams() : array;
}