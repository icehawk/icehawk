<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesApiData;

/**
 * Class DomainRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainRequestHandler
{

	/** @var ServesApiData */
	protected $api;

	/** @var string */
	protected $domain;

	/** @var string */
	protected $demand;

	/** @var string */
	protected $project_namespace;

	/**
	 * @param ServesApiData $api
	 * @param string        $domain
	 * @param string        $demand
	 * @param string        $project_namespace
	 */
	public function __construct( ServesApiData $api, $domain, $demand, $project_namespace )
	{
		$this->api               = $api;
		$this->domain            = $domain;
		$this->demand = $demand;
		$this->project_namespace = $project_namespace;
	}
}