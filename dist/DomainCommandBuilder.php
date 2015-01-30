<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\BuildsDomainCommands;
use Fortuneglobe\IceHawk\Interfaces\ServesApiData;

/**
 * Class DomainCommandBuilder
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommandBuilder implements BuildsDomainCommands
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

	/**
	 * @return string
	 */
	protected function getDomainCamelCase()
	{
		return $this->getStringToCamelCase( $this->domain );
	}

	/**
	 * @return string
	 */
	protected function getCommandCamelCase()
	{
		return $this->getStringToCamelCase( $this->command );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function getStringToCamelCase( $string )
	{
		$words = preg_split( "#[^a-z0-9]#i", $string );
		$words = array_map( 'ucwords', $words );

		return join( '', $words );
	}

	/**
	 * @return string
	 */
	protected function getProjectNamespace()
	{
		return $this->project_namespace;
	}
} 