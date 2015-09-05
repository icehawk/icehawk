<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class MethodNotAllowed
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class MethodNotAllowed extends BaseResponse
{
	/** @var string */
	private $charset;

	/**
	 * @param string $charset
	 */
	public function __construct( $charset = 'utf-8' )
	{
		$this->charset = $charset;
	}

	public function respond()
	{
		header( 'Content-Type: text/plain; charset=' . $this->charset, true, Http::METHOD_NOT_ALLOWED );
		echo "Method not allowed.";
	}
}