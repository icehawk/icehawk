<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

/**
 * Class Options
 * @package Fortuneglobe\IceHawk\Responses
 */
class Options extends AbstractHttpResponse
{
	/** @var array */
	private $allowedRequestMethods;

	public function __construct( array $allowedRequestMethods )
	{
		parent::__construct();

		$this->allowedRequestMethods = $allowedRequestMethods;
	}

	protected function getAdditionalHeaders() : array
	{
		return [
			'Allow: ' . join( ',', $this->allowedRequestMethods ),
		];
	}

	protected function getBody() : string
	{
		return '';
	}
}