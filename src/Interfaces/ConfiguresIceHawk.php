<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Interface ConfiguresIceHawk
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ConfiguresIceHawk
{
	public function getDomainNamespace() : string;

	public function getUriRewriter() : RewritesUri;

	public function getUriResolver() : ResolvesUri;

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array;

	public function getRequestInfo() : ServesRequestInfo;
}
