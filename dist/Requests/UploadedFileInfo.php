<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\WrapsDataOfUploadedFile;

/**
 * Class UploadedFileInfo
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class UploadedFileInfo implements WrapsDataOfUploadedFile
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

	/**
	 * @param string $name
	 * @param string $tmpName
	 * @param string $type
	 * @param int    $size
	 * @param int    $error
	 */
	public function __construct( $name, $tmpName, $type, $size, $error )
	{
		$this->name     = $name;
		$this->tmpName = $tmpName;
		$this->type     = $type;
		$this->size     = $size;
		$this->error    = $error;
	}

	/**
	 * @param array $fileArray
	 *
	 * @return UploadedFileInfo
	 */
	public static function fromFileArray( array $fileArray )
	{
		return new self(
			$fileArray['name'],
			$fileArray['tmp_name'],
			$fileArray['type'],
			$fileArray['size'],
			$fileArray['error']
		);
	}

	/**
	 * @return int
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getTmpName()
	{
		return $this->tmpName;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getRealType()
	{
		return ( new \finfo( FILEINFO_MIME_TYPE ) )->file( $this->tmpName );
	}

	/**
	 * @return string
	 */
	public function getEncoding()
	{
		return ( new \finfo( FILEINFO_MIME_ENCODING ) )->file( $this->tmpName );
	}

	/**
	 * @return bool
	 */
	public function didUploadSucceed()
	{
		return ($this->error == UPLOAD_ERR_OK);
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
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