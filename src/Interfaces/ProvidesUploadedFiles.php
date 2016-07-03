<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesUploadedFiles
 * @package IceHawk\IceHawk\Interfaces
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

	/**
	 * @param string $fieldKey
	 * @param int    $fileIndex
	 *
	 * @return ProvidesUploadedFileData|null
	 */
	public function getOneFile( string $fieldKey, int $fileIndex );
}
