<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesPostRequestData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesPostRequestData extends ServesRequestData, ServesUploadedFiles
{
	public function getRawData() : string;
}
