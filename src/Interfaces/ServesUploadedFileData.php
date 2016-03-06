<?php
/**
 * @author h.woltersdorf
 */
namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Class UploadedFile
 * @package Fortuneglobe\IceHawk\Requests
 */
interface ServesUploadedFileData
{
	/**
	 * @return int
	 */
	public function getError() : int;

	/**
	 * @return string
	 */
	public function getName() : string;

	/**
	 * @return float
	 */
	public function getSize() : float;

	/**
	 * @return string
	 */
	public function getTmpName() : string;

	/**
	 * @return string
	 */
	public function getType() : string;

	/**
	 * @return string
	 */
	public function getRealType() : string;

	/**
	 * @return string
	 */
	public function getEncoding() : string;

	/**
	 * @return bool
	 */
	public function didUploadSucceed() : bool;

	/**
	 * @return string
	 */
	public function getErrorMessage() : string;
}
