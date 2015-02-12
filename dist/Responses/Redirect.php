<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\Http;

/**
 * Class Redirect
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
class Redirect extends BaseResponse
{

	/** @var string */
	private $redirectUrl;

	/** @var string */
	private $redirectCode;

	/**
	 * @param string $redirectUrl
	 * @param string $redirectCode
	 */
	public function __construct( $redirectUrl, $redirectCode = Http::MOVED_PERMANENTLY )
	{
		$this->redirectUrl  = $redirectUrl;
		$this->redirectCode = $redirectCode;
	}

	public function respond()
	{
		session_write_close();

		if ( !empty($this->redirectCode) )
		{
			header( $this->redirectCode );
		}

		header( 'Location: ' . $this->redirectUrl );
		exit();
	}

	/**
	 * @param string $string
	 *
	 * @return bool
	 */
	public function urlEquals( $string )
	{
		return ($string == $this->redirectUrl);
	}

	/**
	 * @param $string
	 *
	 * @return bool
	 */
	public function codeEquals( $string )
	{
		return ($string == $this->redirectCode);
	}
}