<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

/**
 * Class SessionRegistry
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class SessionRegistry
{

	/** @var array */
	private $sessionData;

	/**
	 * @param array $sessionData
	 */
	public function __construct( array &$sessionData )
	{
		$this->sessionData = &$sessionData;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	final protected function setSessionValue( $key, $value )
	{
		$this->sessionData[ $key ] = $value;
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
			return $this->sessionData[ $key ];
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
		return isset($this->sessionData[ $key ]);
	}

	/**
	 * @param string $key
	 */
	final protected function unsetSessionValue( $key )
	{
		if ( $this->isSessionKeySet( $key ) )
		{
			unset($this->sessionData[ $key ]);
		}
	}
}
