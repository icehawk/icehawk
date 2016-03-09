<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFileData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PostRequest
 * @package Fortuneglobe\IceHawk\Requests
 */
final class WriteRequest implements ProvidesWriteRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var array */
	private $postData = [ ];

	/** @var array|ProvidesUploadedFileData[] */
	private $uploadedFiles = [ ];

	public function __construct( ProvidesRequestInfo $requestInfo, array $postData, array $uploadedFiles )
	{
		$this->requestInfo   = $requestInfo;
		$this->postData      = $postData;
		$this->uploadedFiles = $uploadedFiles;
	}

	public function getData() : array
	{
		return $this->postData;
	}

	public function getRawData() : string
	{
		$rawData = file_get_contents( 'php://input' );

		return $rawData ? : '';
	}

	public function getAllFiles() : array
	{
		return $this->getFilesAsInfoObjects();
	}

	/**
	 * @param string $fieldKey
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	public function getFiles( string $fieldKey ) : array
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
	 * @return ProvidesUploadedFileData|null
	 */
	public function getOneFile( string $fieldKey, int $fileIndex = 0 )
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
	 * @return array|ProvidesUploadedFileData[]
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
	public function get( string $key )
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
	 * @return ProvidesRequestInfo
	 */
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}
