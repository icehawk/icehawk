<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Interface ConfiguresIceHawk
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ConfiguresIceHawk
{
	public function getHandlerRootNamespace() : string;

	public function getHandlerPrefixNamespace() : string;

	public function getUriRewriter() : RewritesUri;

	public function getUriResolver() : ResolvesUri;

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array;

	public function getRequestInfo() : ProvidesRequestInfo;

	public function getAllowedRequestMethods() : array;

	public function getFinalResponder() : RespondsFinally;
}
