<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface ServesUriComponents
{
	/**
	 * @return string
	 */
	public function getApiName();

	/**
	 * @return string
	 */
	public function getApiVersion();

	/**
	 * @return string
	 */
	public function getDomain();

	/**
	 * @return string
	 */
	public function getDemand();
}