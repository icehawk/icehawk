<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesEventData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesEventData
{
	public function getRequestInfo() : ServesRequestInfo;
}
