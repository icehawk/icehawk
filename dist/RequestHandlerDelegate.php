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

	/** @var string */
	private $requestMethod;

	/** @var RewritesUri */
	private $uriRewriter;

	/** @var ResolvesUri */
	private $uriResolver;

	/** @var string */
	private $projectNamespace;

	/**
	 * @param string $requestMethod
	 * @param RewritesUri $uriRewriter
	 * @param ResolvesUri $uriResolver
	 * @param string      $projectNamespace
	 */
	public function __construct( $requestMethod, RewritesUri $uriRewriter, ResolvesUri $uriResolver, $projectNamespace )
	{
		$this->requestMethod = $requestMethod;
		$this->uriRewriter      = $uriRewriter;
		$this->uriResolver      = $uriResolver;
		$this->projectNamespace = $projectNamespace;
	}

	/**
	 * @return string
	 */
	public function getRequestMethod()
	{
		return $this->requestMethod;
	}

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter()
	{
		return $this->uriRewriter;
	}

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver()
	{
		return $this->uriResolver;
	}

	/**
	 * @return string
	 */
	public function getProjectNamespace()
	{
		return $this->projectNamespace;
	}
}