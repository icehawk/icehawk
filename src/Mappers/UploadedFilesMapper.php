<?php declare(strict_types=1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Mappers;

use IceHawk\IceHawk\Interfaces\ProvidesUploadedFileData;
use IceHawk\IceHawk\Requests\UploadedFile;

/**
 * Class UploadedFilesMapper
 * @package IceHawk\IceHawk\Mappers
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
		$infoObjects = [];

		foreach ( $files as $fieldName => $filesArray )
		{
			$infoObjects[ $fieldName ] = array_merge(
				$infoObjects[ $fieldName ] ?? [],
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
		$flatArray = [];

		foreach ( $this->uploadedFiles as $field => $fileInfos )
		{
			foreach ( (array)$fileInfos as $keyField => $values )
			{
				if ( is_array( $values ) )
				{
					foreach ( (array)$values as $index => $value )
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
