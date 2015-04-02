<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;

/**
 * Class DomainCommand
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommand
{

	const KEY_SUCCESS_URL = 'success_url';

	const KEY_FAIL_URL    = 'fail_url';

	/** @var ServesPostRequestData */
	protected $request;

	/**
	 * @param ServesPostRequestData $request
	 */
	public function __construct( ServesPostRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( $key )
	{
		return $this->request->get( $key );
	}

	/**
	 * @return bool
	 */
	public function hasSuccessUrl()
	{
		return !is_null( $this->getSuccessUrl() );
	}

	/**
	 * @return string
	 */
	public function getSuccessUrl()
	{
		return $this->getRequestValue( self::KEY_SUCCESS_URL );
	}

	/**
	 * @return bool
	 */
	public function hasFailUrl()
	{
		return !is_null( $this->getFailUrl() );
	}

	/**
	 * @return string
	 */
	public function getFailUrl()
	{
		return $this->getRequestValue( self::KEY_FAIL_URL );
	}
}
