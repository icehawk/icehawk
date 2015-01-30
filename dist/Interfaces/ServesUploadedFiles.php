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
	 * @return array
	 */
	public function getFiles( $field_key );
}