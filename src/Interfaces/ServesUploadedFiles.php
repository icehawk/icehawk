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
	 * @return array|WrapsDataOfUploadedFile[][]
	 */
	public function getAllFiles();

	/**
	 * @param string $fieldKey
	 *
	 * @return array|WrapsDataOfUploadedFile[]
	 */
	public function getFiles( $fieldKey );

	/**
	 * @param string $fieldKey
	 * @param int    $fileIndex
	 *
	 * @return WrapsDataOfUploadedFile|null
	 */
	public function getOneFile( $fieldKey, $fileIndex );
}
