<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Responses;

use IceHawk\IceHawk\Constants\HttpCode;

/**
 * Class MethodNotImplemented
 * @package IceHawk\IceHawk\Responses
 */
final class MethodNotImplemented extends AbstractHttpResponse
{
	/** @var string */
	private $requestMethod;

	public function __construct( string $requestMethod )
	{
		parent::__construct( 'text/plain', HttpCode::NOT_IMPLEMENTED );

		$this->requestMethod = $requestMethod;
	}

	protected function getBody() : string
	{
		return sprintf( '%d - Method Not Implemented (%s)', $this->getHttpCode(), $this->requestMethod );
	}
}