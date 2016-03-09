<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Demands;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFileData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class Command
 * @package Fortuneglobe\IceHawk\Demands
 */
abstract class Command
{

	/** @var ProvidesWriteRequestData */
	protected $request;

	/**
	 * @param ProvidesWriteRequestData $request
	 */
	public function __construct( ProvidesWriteRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( string $key )
	{
		return $this->request->get( $key );
	}

	/**
	 * @return array
	 */
	protected function getRequestData() : array
	{
		return $this->request->getData();
	}

	/**
	 * @return string
	 */
	protected function getRequestRawData() : string
	{
		return $this->request->getRawData();
	}

	/**
	 * @return array|ProvidesUploadedFileData[][]
	 */
	protected function getAllUploadedFiles() : array
	{
		return $this->request->getAllFiles();
	}

	/**
	 * @param string $key
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	protected function getUploadedFiles( string $key ) : array
	{
		return $this->request->getFiles( $key );
	}

	/**
	 * @param string $key
	 * @param int    $fileIndex
	 *
	 * @return ProvidesUploadedFileData|null
	 */
	protected function getOneUploadedFile( string $key, int $fileIndex = 0 )
	{
		return $this->request->getOneFile( $key, $fileIndex );
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	final public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}
}
