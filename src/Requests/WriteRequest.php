<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesUploadedFileData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class PostRequest
 * @package IceHawk\IceHawk\Requests
 */
final class WriteRequest implements ProvidesWriteRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;
	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var array
	 */
	private $uploadedFiles;

	public function __construct( ProvidesRequestInfo $requestInfo, array $data, string $body, array $uploadedFiles = [ ] )
	{
		$this->requestInfo   = $requestInfo;
		$this->body          = $body;
		$this->data     = $data;
		$this->uploadedFiles = $uploadedFiles;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInputData() : array
	{
		return $this->data;
	}

	/**
	 * @param string $key
	 * @param null|string|array $default
	 *
	 * @return null|string|array
	 */
	public function get( string $key, $default = null )
	{
		return $this->data[ $key ] ?? $default;
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
		return $this->uploadedFiles[ $fieldKey ] ?? [ ];
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
