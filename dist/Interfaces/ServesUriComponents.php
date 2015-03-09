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
	public function getDomain();

	/**
	 * @return string
	 */
	public function getDemand();

	/**
	 * @return array
	 */
	public function getParams();
}