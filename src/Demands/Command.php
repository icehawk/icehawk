<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Demands;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFileData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestInputData;

/**
 * Class Command
 *
 * @package Fortuneglobe\IceHawk\Demands
 */
abstract class Command
{

	/** @var ProvidesRequestInfo */
	protected $requestInfo;

	/**
	 * @var ProvidesWriteRequestInputData
	 */
	protected $requestInput;

	/**
	 * @param ProvidesWriteRequestData $request
	 */
	public function __construct( ProvidesWriteRequestData $request )
	{
		$this->requestInfo  = $request->getRequestInfo();
		$this->requestInput = $request->getInputData();
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( string $key )
	{
		return $this->requestInput->get( $key );
	}

	/**
	 * @return array
	 */
	protected function getRequestData() : array
	{
		return $this->requestInput->getData();
	}

	/**
	 * @return string
	 */
	protected function getRequestRawData() : string
	{
		return $this->requestInput->getBody();
	}

	/**
	 * @return array|ProvidesUploadedFileData[][]
	 */
	protected function getAllUploadedFiles() : array
	{
		return $this->requestInput->getAllFiles();
	}

	/**
	 * @param string $key
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	protected function getUploadedFiles( string $key ) : array
	{
		return $this->requestInput->getFiles( $key );
	}

	/**
	 * @param string $key
	 * @param int    $fileIndex
	 *
	 * @return ProvidesUploadedFileData|null
	 */
	protected function getOneUploadedFile( string $key, int $fileIndex = 0 )
	{
		return $this->requestInput->getOneFile( $key, $fileIndex );
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	final public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}
