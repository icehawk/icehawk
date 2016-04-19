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
	private $postData;

	/** @var array|ProvidesUploadedFileData[] */
	private $uploadedFiles;

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

	public function getBody() : string
	{
		$body = file_get_contents( 'php://input' );

		return $body ? : '';
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
	public function getOneFile( string $fieldKey, int $fileIndex = 0 ) : ProvidesUploadedFileData
	{
		$files = $this->getFiles( $fieldKey );

		return $files[ $fileIndex ] ?? null;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key )
	{
		return $this->postData[ $key ] ?? null;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}
