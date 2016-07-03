<?php
namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ProvidesWriteRequestInputData
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesWriteRequestInputData extends ProvidesRequestInputData, ProvidesUploadedFiles
{
	public function getBody() : string;
}