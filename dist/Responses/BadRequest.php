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
		echo join( "\n", $this->messages );
		exit();
	}
}