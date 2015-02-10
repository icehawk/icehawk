<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

/**
 * Class SessionRegistry
 *
 * @package Fortuneglobe\IceHawk
 */
class SessionRegistry
{

	/** @var array */
	private $session_data;

	/**
	 * @param array $session_data
	 */
	public function __construct( array &$session_data )
	{
		$this->session_data = &$session_data;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	final protected function setSessionValue( $key, $value )
	{
		$this->session_data[ $key ] = $value;
	}

	/**
	 * @param string $key
	 *
	 * @return null|mixed
	 */
	final protected function getSessionValue( $key )
	{
		if ( $this->isSessionKeySet( $key ) )
		{
			return $this->session_data[ $key ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	final protected function isSessionKeySet( $key )
	{
		return isset($this->session_data[ $key ]);
	}

	/**
	 * @param string $key
	 */
	final protected function unsetSessionValue( $key )
	{
		if ( $this->isSessionKeySet( $key ) )
		{
			unset($this->session_data[ $key ]);
		}
	}
}