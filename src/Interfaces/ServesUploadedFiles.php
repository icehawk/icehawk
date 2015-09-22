<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesUploadedFiles
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesUploadedFiles
{
	/**
	 * @return array|ServesUploadedFileData[][]
	 */
	public function getAllFiles();

	/**
	 * @param string $fieldKey
	 *
	 * @return array|ServesUploadedFileData[]
	 */
	public function getFiles( $fieldKey );

	/**
	 * @param string $fieldKey
	 * @param int    $fileIndex
	 *
	 * @return ServesUploadedFileData|null
	 */
	public function getOneFile( $fieldKey, $fileIndex );
}
