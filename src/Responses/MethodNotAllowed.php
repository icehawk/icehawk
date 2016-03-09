<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;

/**
 * Class MethodNotAllowed
 * @package Fortuneglobe\IceHawk\Responses
 */
class MethodNotAllowed extends AbstractHttpResponse
{
	public function __construct()
	{
		parent::__construct( 'text/plain', HttpCode::METHOD_NOT_ALLOWED );
	}

	protected function getBody() : string
	{
		return '405 - Method Not Allowed.';
	}

}