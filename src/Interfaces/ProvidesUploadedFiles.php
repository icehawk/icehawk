<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesUploadedFiles
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesUploadedFiles
{
	/**
	 * @return array|ProvidesUploadedFileData[][]
	 */
	public function getAllFiles() : array;

	/**
	 * @param string $fieldKey
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	public function getFiles( string $fieldKey ) : array;

	public function getOneFile( string $fieldKey, int $fileIndex ) : ProvidesUploadedFileData;
}
