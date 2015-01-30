<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestHandlerConfig;

/**
 * Class RequestHandlerDelegate
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestHandlerDelegate implements ServesRequestHandlerConfig
{

	/** @var RewritesUri */
	private $uri_rewriter;

	/** @var ResolvesUri */
	private $uri_resolver;

	/** @var string */
	private $project_namespace;

	/**
	 * @param RewritesUri $rewrite_map
	 * @param ResolvesUri $uri_component_resolver
	 * @param string      $project_namespace
	 */
	public function __construct( RewritesUri $rewrite_map, ResolvesUri $uri_component_resolver, $project_namespace )
	{
		$this->uri_rewriter      = $rewrite_map;
		$this->uri_resolver      = $uri_component_resolver;
		$this->project_namespace = $project_namespace;
	}

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter()
	{
		return $this->uri_rewriter;
	}

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver()
	{
		return $this->uri_resolver;
	}

	/**
	 * @return string
	 */
	public function getProjectNamespace()
	{
		return $this->project_namespace;
	}
}