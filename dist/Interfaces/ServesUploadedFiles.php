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
	 * @param string $field_key
	 *
	 * @return WrapsDataOfUploadedFile[]
	 */
	public function getFiles( $field_key );

	/**
	 * @param string $field_key
	 * @param int    $file_index
	 *
	 * @return WrapsDataOfUploadedFile|null
	 */
	public function getOneFile( $field_key, $file_index );
}