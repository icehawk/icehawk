<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class NotFound
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class NotFound extends BaseResponse
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
		header( 'Content-Type: text/plain; charset=' . $this->charset, true, Http::NOT_FOUND );
		echo "Not Found.";
	}
}