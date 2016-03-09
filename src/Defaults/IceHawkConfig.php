<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinally;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfig
 * @package Fortuneglobe\IceHawk
 */
class IceHawkConfig implements ConfiguresIceHawk
{
	public function getUriRewriter() : RewritesUri
	{
		return new UriRewriter();
	}

	public function getUriResolver() : ResolvesUri
	{
		return new UriResolver();
	}

	public function getHandlerRootNamespace() : string
	{
		return __NAMESPACE__;
	}

	public function getHandlerPrefixNamespace() : string
	{
		return '';
	}

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array
	{
		return [ ];
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return RequestInfo::fromEnv();
	}

	public function getAllowedRequestMethods() : array
	{
		return HttpMethod::ALL_METHODS;
	}

	public function getFinalResponder() : RespondsFinally
	{
		return new FinalResponder();
	}
}
