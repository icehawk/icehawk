<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class Unauthorized
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class Unauthorized extends BaseResponse
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
		header( 'WWW-Authenticate: Basic realm="Authentication"' );
		header( 'Content-Type: text/plain; charset=' . $this->charset, true, Http::UNAUTHORIZED );
		echo "Unauthorized.";
		exit();
	}
}