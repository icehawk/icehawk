<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesApiData;

/**
 * Class DomainRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainRequestHandler implements HandlesDomainRequests
{

	/** @var ServesApiData */
	protected $api;

	/** @var string */
	protected $domain;

	/** @var string */
	protected $command;

	/** @var string */
	protected $project_namespace;

	/**
	 * @param ServesApiData $api
	 * @param string        $domain
	 * @param string        $command
	 * @param string        $project_namespace
	 */
	public function __construct( ServesApiData $api, $domain, $command, $project_namespace )
	{
		$this->api               = $api;
		$this->domain            = $domain;
		$this->command           = $command;
		$this->project_namespace = $project_namespace;
	}
}