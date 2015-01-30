<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

/**
 * Class JsonData
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
class JsonData
{

	/** @var mixed */
	private $json_data;

	/** @var string */
	private $charset;

	/**
	 * @param mixed  $json_data
	 * @param string $charset
	 */
	public function __construct( $json_data, $charset = 'utf-8' )
	{
		$this->json_data = $json_data;
		$this->charset   = $charset;
	}

	public function respond()
	{
		header( 'Content-Type: application/json; charset=' . $this->charset );
		echo json_encode( $this->json_data );
	}
}
