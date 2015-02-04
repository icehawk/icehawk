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

	/**
	 * @param array $messages
	 */
	public function __construct( array $messages )
	{
		$this->messages = $messages;
	}

	public function respond()
	{
		header( Http::BAD_REQUEST );
		echo json_encode( [ 'messages' => $this->messages ] );
		exit();
	}
}