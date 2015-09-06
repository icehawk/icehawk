<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class InternalServerError
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
class InternalServerError extends BaseResponse
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
		header( 'Content-Type: text/plain; charset=' . $this->charset, true, Http::INTERNAL_SERVER_ERROR );
		echo "Internal server error.";
	}
}