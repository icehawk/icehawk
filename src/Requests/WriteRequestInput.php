<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesUploadedFileData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestInputData;

/**
 * Class RequestInput
 * @package IceHawk\IceHawk\Requests
 */
final class WriteRequestInput implements ProvidesWriteRequestInputData
{
	/** @var string */
	private $body;

	/** @var array */
	private $data;

	/** @var array|ProvidesUploadedFileData[] */
	private $uploadedFiles;

	public function __construct( string $body, array $data, array $uploadedFiles = [] )
	{
		$this->body          = $body;
		$this->data          = $data;
		$this->uploadedFiles = $uploadedFiles;
	}

	public function getData() : array
	{
		return $this->data;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key )
	{
		return $this->data[ $key ] ?? null;
	}

	public function getBody() : string
	{
		return $this->body;
	}

	public function getAllFiles() : array
	{
		return $this->uploadedFiles;
	}

	/**
	 * @param string $fieldKey
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	public function getFiles( string $fieldKey ) : array
	{
		return $this->uploadedFiles[ $fieldKey ] ?? [];
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

		return $files[ $fileIndex ] ?? null;
	}
}
