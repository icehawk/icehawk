<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Apis;

use Fortuneglobe\IceHawk\Api;

/**
 * Class Web
 *
 * @package Fortuneglobe\IceHawk\Apis
 */
final class Web extends Api
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return Api::WEB;
	}
}