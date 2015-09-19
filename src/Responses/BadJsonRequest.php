<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class BadJsonRequest
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
final class BadJsonRequest extends BaseResponse
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
		header( 'Content-Type: application/json; charset=' . $this->charset, true, Http::BAD_REQUEST );
		echo json_encode( [ 'messages' => $this->messages ] );
	}
}