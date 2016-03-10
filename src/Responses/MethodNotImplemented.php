<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;

/**
 * Class MethodNotImplemented
 * @package Fortuneglobe\IceHawk\Responses
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
		return sprintf( '501 - Method Not Implemented (%s)', $this->requestMethod );
	}
}