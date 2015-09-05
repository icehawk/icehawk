<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class BadRequest
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
class BadRequest extends BaseResponse
{

	/** @var array */
	private $messages;

	/** @var string */
	private $charset;

	/**
	 * @param array  $messages
	 * @param string $charset
	 */
	public function __construct( array $messages, $charset = 'utf-8' )
	{
		$this->messages = $messages;
		$this->charset = $charset;
	}

	public function respond()
	{
		header( 'Content-Type: text/plain; charset=' . $this->charset, true, Http::BAD_REQUEST );
		echo join( "\n", $this->messages );
	}
}