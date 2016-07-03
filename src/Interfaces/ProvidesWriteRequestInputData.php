<?php
namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ProvidesWriteRequestInputData
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesWriteRequestInputData extends ProvidesRequestInputData, ProvidesUploadedFiles
{
	public function getBody() : string;
}