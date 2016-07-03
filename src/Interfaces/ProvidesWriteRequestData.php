<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesPostRequestData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesWriteRequestData extends ProvidesRequestData
{
	public function getInputData() : ProvidesWriteRequestInputData;
}
