<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesCommandData
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesCommandData
{
	/**
	 * @return bool
	 */
	public function isValid();

	/**
	 * @return array
	 */
	public function getValidationMessages();

	public function execute();

	/**
	 * @return string
	 */
	public function getDomain();

	/**
	 * @return ServesApiData
	 */
	public function getApi();

	/**
	 * @return bool
	 */
	public function isExecutable();

	/**
	 * @return ServesResponse
	 */
	public function getResponder();
}