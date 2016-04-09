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
	/** @var string */
	private $requestMethod;

	public function __construct( string $requestMethod )
	{
		parent::__construct( 'text/plain', HttpCode::METHOD_NOT_ALLOWED );

		$this->requestMethod = $requestMethod;
	}

	protected function getBody() : string
	{
		return sprintf( '%d - Method Not Allowed (%s).', $this->getHttpCode(), $this->requestMethod );
	}

}