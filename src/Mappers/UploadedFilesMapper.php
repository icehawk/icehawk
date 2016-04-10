<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Mappers;

use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFileData;
use Fortuneglobe\IceHawk\Requests\UploadedFile;

/**
 * Class UploadedFilesMapper
 * @package Fortuneglobe\IceHawk\Mappers
 */
final class UploadedFilesMapper
{
	/** @var array */
	private $uploadedFiles;

	/**
	 * @param array $uploadedFiles
	 */
	public function __construct( array $uploadedFiles )
	{
		$this->uploadedFiles = $uploadedFiles;
	}

	/**
	 * @return array|ProvidesUploadedFileData
	 */
	public function mapToInfoObjects() : array
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
	private function getFilesAsNameIndexedArray() : array
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
}