<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Responses;

/**
 * Class Options
 * @package IceHawk\IceHawk\Responses
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