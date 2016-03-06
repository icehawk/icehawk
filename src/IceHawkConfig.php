<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ListensToEvents;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class IceHawkConfig
 *
 * @package Fortuneglobe\IceHawk
 */
class IceHawkConfig implements ServesIceHawkConfig
{
	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter()
	{
		return new UriRewriter();
	}

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver()
	{
		return new UriResolver();
	}

	/**
	 * @return string
	 */
	public function getDomainNamespace()
	{
		return __NAMESPACE__;
	}

	/**
	 * @return array|ListensToEvents[]
	 */
	public function getEventSubscribers()
	{
		return [ ];
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return RequestInfo::fromEnv();
	}
}
