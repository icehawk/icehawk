<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Apis;

use Fortuneglobe\IceHawk\Api;

/**
 * Class Json
 *
 * @package Fortuneglobe\IceHawk\Apis
 */
final class Json extends Api
{

	/**
	 * @return string
	 */
	public function getName()
	{
		return Api::JSON;
	}
}