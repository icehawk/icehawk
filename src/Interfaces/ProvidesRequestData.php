<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesRequestData
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestData
{
	public function getInfo() : ProvidesRequestInfo;
} 
