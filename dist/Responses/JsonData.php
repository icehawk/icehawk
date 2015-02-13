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
	private $jsonData;

	/** @var string */
	private $charset;

	/**
	 * @param mixed $jsonData
	 * @param string $charset
	 */
	public function __construct( $jsonData, $charset = 'utf-8' )
	{
		$this->jsonData = $jsonData;
		$this->charset  = $charset;
	}

	public function respond()
	{
		header( 'Content-Type: application/json; charset=' . $this->charset, true );
		echo json_encode( $this->jsonData );
	}
}
