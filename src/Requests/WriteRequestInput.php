<?php
namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesUploadedFileData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestInputData;

/**
 * Class RequestInput
 * @package IceHawk\IceHawk\Requests
 */
final class WriteRequestInput implements ProvidesWriteRequestInputData
{
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
