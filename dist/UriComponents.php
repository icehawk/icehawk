<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class UriComponents
 *
 * @package Fortuneglobe\IceHawk
 */
class UriComponents implements ServesUriComponents
{

	/** @var string */
	private $api;

	/** @var string */
	private $api_version;

	/** @var string */
	private $domain;

	/** @var string */
	private $command;

	/**
	 * @param string $api
	 * @param string $api_version
	 * @param string $domain
	 * @param string $command
	 */
	public function __construct( $api, $api_version, $domain, $command )
	{
		$this->api         = $api;
		$this->api_version = $api_version;
		$this->domain      = $domain;
		$this->command     = $command;
	}

	/**
	 * @return string
	 */
	public function getApi()
	{
		return $this->api;
	}

	/**
	 * @return string
	 */
	public function getApiVersion()
	{
		return $this->api_version;
	}

	/**
	 * @return string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * @return string
	 */
	public function getCommand()
	{
		return $this->command;
	}
}