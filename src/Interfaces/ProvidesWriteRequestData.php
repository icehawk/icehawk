<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesPostRequestData
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesWriteRequestData extends ProvidesRequestData, ProvidesUploadedFiles
{
	public function getBody() : string;
}
