<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfig
 * @package Fortuneglobe\IceHawk
 */
class IceHawkConfig implements ConfiguresIceHawk
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
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers()
	{
		return [
			new EventSubscriber(),
		];
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return RequestInfo::fromEnv();
	}
}
