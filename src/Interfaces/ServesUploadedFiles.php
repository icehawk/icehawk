<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesUploadedFiles
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesUploadedFiles
{
	/**
	 * @return array|ServesUploadedFileData[][]
	 */
	public function getAllFiles() : array;

	/**
	 * @param string $fieldKey
	 *
	 * @return array|ServesUploadedFileData[]
	 */
	public function getFiles( string $fieldKey ) : array;

	public function getOneFile( string $fieldKey, int $fileIndex ) : ServesUploadedFileData;
}
