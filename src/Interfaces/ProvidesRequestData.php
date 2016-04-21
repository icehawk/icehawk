<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesRequestData
{
	public function getRequestInfo() : ProvidesRequestInfo;
} 