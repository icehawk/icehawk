<?php
/**
 * POST request wrapper
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;

/**
 * Class PostRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class PostRequest implements ServesPostRequestData
{

	/** @var array */
	private $postData = [ ];

	/** @var array */
	private $uploadedFiles = [ ];

	/**
	 * @param array $postData
	 * @param array $uploadedFiles
	 */
	public function __construct( array $postData, array $uploadedFiles )
	{
		$this->postData      = $postData;
		$this->uploadedFiles = $uploadedFiles;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->postData;
	}

	/**
	 * @return array
	 */
	public function getAllFiles()
	{
		return $this->getFilesAsInfoObjects();
	}

	/**
	 * @param $fieldKey
	 *
	 * @return UploadedFileInfo[]
	 */
	public function getFiles( $fieldKey )
	{
		$allFiles = $this->getAllFiles();

		if ( isset($allFiles[ $fieldKey ]) )
		{
			return $allFiles[ $fieldKey ];
		}
		else
		{
			return [ ];
		}
	}

	/**
	 * @param string $fieldKey
	 * @param int    $fileIndex
	 *
	 * @return UploadedFileInfo|null
	 */
	public function getOneFile( $fieldKey, $fileIndex = 0 )
	{
		$files = $this->getFiles( $fieldKey );

		if ( isset($files[ intval( $fileIndex ) ]) )
		{
			return $files[ $fileIndex ];
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
		$files       = $this->getFilesAsNameIndexedArray();
		$infoObjects = [ ];

		foreach ( $files as $fieldName => $filesArray )
		{
			$infoObjects[ $fieldName ] = array_merge(
				isset($infoObjects[ $fieldName ]) ? $infoObjects[ $fieldName ] : [ ],
				array_map( [ UploadedFileInfo::class, 'fromFileArray' ], $filesArray )
			);
		}

		return $infoObjects;
	}

	/**
	 * @return array
	 */
	private function getFilesAsNameIndexedArray()
	{
		$flatArray = [ ];

		foreach ( $this->uploadedFiles as $field => $fileInfos )
		{
			foreach ( $fileInfos as $keyField => $values )
			{
				if ( is_array( $values ) )
				{
					foreach ( $values as $index => $value )
					{
						$flatArray[ $field ][ $index ][ $keyField ] = $value;
					}
				}
				else
				{
					$flatArray[ $field ][0][ $keyField ] = $values;
				}
			}
		}

		return $flatArray;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( $key )
	{
		if ( isset($this->postData[ $key ]) )
		{
			return $this->postData[ $key ];
		}
		else
		{
			return null;
		}
	}
}