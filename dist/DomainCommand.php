<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\WrapsDataOfUploadedFile;

/**
 * Class DomainCommand
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommand
{

	/** @var ServesPostRequestData */
	protected $request;

	/**
	 * @param ServesPostRequestData $request
	 */
	public function __construct( ServesPostRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( $key )
	{
		return $this->request->get( $key );
	}

	/**
	 * @return array
	 */
	protected function getRequestData()
	{
		return $this->request->getData();
	}

	/**
	 * @return null|string
	 */
	protected function getRequestRawData()
	{
		return $this->request->getRawData();
	}

	/**
	 * @return array|WrapsDataOfUploadedFile[][]
	 */
	protected function getAllUploadedFiles()
	{
		return $this->request->getAllFiles();
	}

	/**
	 * @param string $key
	 *
	 * @return array|WrapsDataOfUploadedFile[]
	 */
	protected function getUploadedFiles( $key )
	{
		return $this->request->getFiles( $key );
	}

	/**
	 * @param string $key
	 * @param int    $fileIndex
	 *
	 * @return WrapsDataOfUploadedFile|null
	 */
	protected function getOneUploadedFile( $key, $fileIndex = 0 )
	{
		return $this->request->getOneFile( $key, $fileIndex );
	}
}
