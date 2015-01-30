<?php
/**
 * POST request wrapper
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUploadedFiles;

/**
 * Class PostRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class PostRequest implements ServesRequestData, ServesUploadedFiles
{

	/** @var array */
	private $post_data = [ ];

	/** @var array */
	private $uploaded_files = [ ];

	/**
	 * @param array $post_data
	 * @param array $uploaded_files
	 */
	public function __construct( array $post_data, array $uploaded_files )
	{
		$this->post_data      = $post_data;
		$this->uploaded_files = $uploaded_files;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->post_data;
	}

	/**
	 * @return array
	 */
	public function getAllFiles()
	{
		return $this->getFilesAsInfoObjects();
	}

	/**
	 * @param $field_key
	 *
	 * @return UploadedFileInfo[]
	 */
	public function getFiles( $field_key )
	{
		$all_files = $this->getAllFiles();

		if ( isset($all_files[ $field_key ]) )
		{
			return $all_files[ $field_key ];
		}
		else
		{
			return [ ];
		}
	}

	/**
	 * @param string $field_key
	 * @param int    $file_index
	 *
	 * @return UploadedFileInfo|null
	 */
	public function getOneFile( $field_key, $file_index = 0 )
	{
		$files = $this->getFiles( $field_key );

		if ( isset($files[ intval( $file_index ) ]) )
		{
			return $files[ $file_index ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return array
	 */
	private function getFilesAsInfoObjects()
	{
		$files        = $this->getFilesAsNameIndexedArray();
		$info_objects = [ ];

		foreach ( $files as $field_name => $files_array )
		{
			$info_objects[ $field_name ] = array_merge(
				isset($info_objects[ $field_name ]) ? $info_objects[ $field_name ] : [ ],
				array_map( [ UploadedFileInfo::class, 'fromFileArray' ], $files_array )
			);
		}

		return $info_objects;
	}

	/**
	 * @return array
	 */
	private function getFilesAsNameIndexedArray()
	{
		$flat_array = [ ];

		foreach ( $this->uploaded_files as $field => $file_infos )
		{
			foreach ( $file_infos as $key_field => $values )
			{
				if ( is_array( $values ) )
				{
					foreach ( $values as $index => $value )
					{
						$flat_array[ $field ][ $index ][ $key_field ] = $value;
					}
				}
				else
				{
					$flat_array[ $field ][0][ $key_field ] = $values;
				}
			}
		}

		return $flat_array;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( $key )
	{
		if ( isset($this->post_data[ $key ]) )
		{
			return $this->post_data[ $key ];
		}
		else
		{
			return null;
		}
	}
}