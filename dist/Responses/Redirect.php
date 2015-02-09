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
	private $redirect_url;

	/** @var string */
	private $redirect_code;

	/**
	 * @param string $redirect_url
	 * @param string $redirect_code
	 */
	public function __construct( $redirect_url, $redirect_code = Http::MOVED_PERMANENTLY )
	{
		$this->redirect_url  = $redirect_url;
		$this->redirect_code = $redirect_code;
	}

	public function respond()
	{
		session_write_close();

		if ( !empty($this->redirect_code) )
		{
			header( $this->redirect_code );
		}

		header( 'Location: ' . $this->redirect_url );
		exit();
	}

	/**
	 * @param string $string
	 *
	 * @return bool
	 */
	public function urlEquals( $string )
	{
		return $string == $this->redirect_url;
	}
}