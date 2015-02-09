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
	private $demand;

	/**
	 * @param string $api
	 * @param string $api_version
	 * @param string $domain
	 * @param string $demand
	 */
	public function __construct( $api, $api_version, $domain, $demand )
	{
		$this->api         = $api;
		$this->api_version = $api_version;
		$this->domain      = $domain;
		$this->demand = $demand;
	}

	/**
	 * @return string
	 */
	public function getApiName()
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
	public function getDemand()
	{
		return $this->demand;
	}
}