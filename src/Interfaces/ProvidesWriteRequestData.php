<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesPostRequestData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesWriteRequestData extends ProvidesRequestData, ProvidesUploadedFiles
{
	public function getBody() : string;
}
