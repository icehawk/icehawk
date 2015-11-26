<?php
/**
 * POST request wrapper
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUploadedFileData;

/**
 * Class PostRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class PostRequest implements ServesPostRequestData
{
	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var array */
	private $postData = [ ];

	/** @var array|ServesUploadedFileData[] */
	private $uploadedFiles = [ ];

	/**
	 * PostRequest constructor.
	 *
	 * @param ServesRequestInfo              $requestInfo
	 * @param array                          $postData
	 * @param array|ServesUploadedFileData[] $uploadedFiles
	 */
	public function __construct( ServesRequestInfo $requestInfo, array $postData, array $uploadedFiles )
	{
		$this->requestInfo   = $requestInfo;
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
	 * @return null|string
	 */
	public function getRawData()
	{
		$rawData = file_get_contents( 'php://input' );

		return $rawData ?: null;
	}

	/**
	 * @return array
	 */
	public function getAllFiles()
	{
		return $this->getFilesAsInfoObjects();
	}

	/**
	 * @param string $fieldKey
	 *
	 * @return ServesUploadedFileData[]
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
	 * @return ServesUploadedFileData|null
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
	 * @return array|ServesUploadedFileData[]
	 */
	private function getFilesAsInfoObjects()
	{
		$files       = $this->getFilesAsNameIndexedArray();
		$infoObjects = [ ];

		foreach ( $files as $fieldName => $filesArray )
		{
			$infoObjects[ $fieldName ] = array_merge(
				isset($infoObjects[ $fieldName ]) ? $infoObjects[ $fieldName ] : [ ],
				array_map( [ UploadedFile::class, 'fromFileArray' ], $filesArray )
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

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return $this->requestInfo;
	}
}
