<?php
/**
 *
 * @author hollodotme
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
	 * @return array
	 */
	public function getAllFiles();

	/**
	 * @param string $fieldKey
	 *
	 * @return WrapsDataOfUploadedFile[]
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