<?php declare(strict_types = 1);
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

/**
 * Class UploadedFile
 * @package IceHawk\IceHawk\Requests
 */
final class UploadedFile implements ProvidesUploadedFileData
{

	/** @var string */
	private $name;

	/** @var string */
	private $tmpName;

	/** @var int */
	private $error;

	/** @var int */
	private $size;

	/** @var string */
	private $type;

	public function __construct( string $name, string $tmpName, string $type, int $size, int $error )
	{
		$this->name    = $name;
		$this->tmpName = $tmpName;
		$this->type    = $type;
		$this->size    = $size;
		$this->error   = $error;
	}

	public static function fromFileArray( array $fileArray ) : self
	{
		return new self(
			$fileArray['name'],
			$fileArray['tmp_name'],
			$fileArray['type'],
			$fileArray['size'],
			$fileArray['error']
		);
	}

	public function getError() : int
	{
		return $this->error;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getSize() : int
	{
		return $this->size;
	}

	public function getTmpName() : string
	{
		return $this->tmpName;
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function getRealType() : string
	{
		if ( !empty($this->tmpName) )
		{
			return (new \finfo( FILEINFO_MIME_TYPE ))->file( $this->tmpName );
		}

		return '';
	}

	public function getEncoding() : string
	{
		if ( !empty($this->tmpName) )
		{
			return (new \finfo( FILEINFO_MIME_ENCODING ))->file( $this->tmpName );
		}

		return '';
	}

	public function didUploadSucceed() : bool
	{
		return ($this->error === UPLOAD_ERR_OK);
	}

	public function getErrorMessage() : string
	{
		switch ( $this->error )
		{
			case UPLOAD_ERR_CANT_WRITE:
				$message = 'Cannot write file.';
				break;

			case UPLOAD_ERR_EXTENSION:
				$message = 'Upload canceled by PHP extension.';
				break;

			case UPLOAD_ERR_FORM_SIZE:
				$message = 'Filesize exceeded max size allowed by input form.';
				break;

			case UPLOAD_ERR_INI_SIZE:
				$message = 'Filesize exceeded max size allowed by server.';
				break;

			case UPLOAD_ERR_NO_FILE:
				$message = 'No file uploaded.';
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				$message = 'No upload temp directory available.';
				break;

			case UPLOAD_ERR_PARTIAL:
				$message = 'File was uploaded partially.';
				break;

			case UPLOAD_ERR_OK:
			default:
				$message = '';
				break;
		}

		return $message;
	}
}
