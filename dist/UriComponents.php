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
	private $domain;

	/** @var string */
	private $demand;

	/**
	 * @param string $domain
	 * @param string $demand
	 */
	public function __construct( $domain, $demand )
	{
		$this->domain = $domain;
		$this->demand = $demand;
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