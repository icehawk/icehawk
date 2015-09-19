<?php
/**
 * @author h.woltersdorf
 */
namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Class UploadedFileInfo
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
interface WrapsDataOfUploadedFile
{
	/**
	 * @return int
	 */
	public function getError();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return int
	 */
	public function getSize();

	/**
	 * @return string
	 */
	public function getTmpName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getRealType();

	/**
	 * @return string
	 */
	public function getEncoding();

	/**
	 * @return bool
	 */
	public function didUploadSucceed();

	/**
	 * @return string
	 */
	public function getErrorMessage();
}