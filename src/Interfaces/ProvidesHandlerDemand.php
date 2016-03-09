<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesUriComponents
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesHandlerDemand
{
	public function getDomain() : string;

	public function getDemand() : string;

	public function getParams() : array;
}