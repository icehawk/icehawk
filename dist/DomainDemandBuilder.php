<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

/**
 * Class DomainDemandBuilder
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainDemandBuilder
{

	/** @var string */
	protected $domain;

	/** @var string */
	protected $demand;

	/** @var string */
	protected $project_namespace;

	/**
	 * @param string $domain
	 * @param string $demand
	 * @param string $project_namespace
	 */
	public function __construct( $domain, $demand, $project_namespace )
	{
		$this->domain            = $domain;
		$this->demand = $demand;
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
	protected function getActionCamelCase()
	{
		return $this->getStringToCamelCase( $this->demand );
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