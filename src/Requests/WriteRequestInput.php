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

	/** @var resource */
	private $inputStream;

	/** @var array */
	private $data;

	/** @var array|ProvidesUploadedFileData[] */
	private $uploadedFiles;

	public function __construct( array $data, array $uploadedFiles = [] )
	{
		$this->data          = $data;
		$this->uploadedFiles = $uploadedFiles;
	}

	public function getData() : array
	{
		return $this->data;
	}

	/**
	 * @param string            $key
	 * @param string|array|null $default
	 *
	 * @return string|array|null
	 */
	public function get( string $key, $default = null )
	{
		return $this->data[ $key ] ?? $default;
	}

	public function getBody() : string
	{
		if ( null === $this->body )
		{
			$stream = $this->getBodyAsStream();
			$body   = @stream_get_contents( $stream );

			$this->body = $body ? : '';
		}

		return $this->body;
	}

	/**
	 * @return bool|resource
	 */
	public function getBodyAsStream()
	{
		if ( null === $this->inputStream )
		{
			$this->inputStream = fopen( 'php://input', 'rb' );
		}

		return $this->inputStream;
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
	 * @param string     $fieldKey
	 * @param int|string $fileIndex
	 *
	 * @return ProvidesUploadedFileData
	 */
	public function getOneFile( string $fieldKey, $fileIndex = 0 ) : ProvidesUploadedFileData
	{
		$files = $this->getFiles( $fieldKey );

		return $files[ $fileIndex ] ?? new UploadedFile( '', '', '', 0, UPLOAD_ERR_NO_FILE );
	}
}
