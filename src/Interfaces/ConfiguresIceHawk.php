<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\RequestParsers\AbstractBodyParserFactory;

/**
 * Interface ConfiguresIceHawk
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ConfiguresIceHawk
{
	public function getUriRewriter() : RewritesUri;

	public function getReadRequestResolver() : ResolvesReadRequest;

	public function getWriteRequestResolver() : ResolvesWriteRequest;

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array;

	public function getRequestInfo() : ProvidesRequestInfo;

	public function getFinalReadRequestResponder() : RespondsFinallyToReadRequest;

	public function getFinalWriteRequestResponder() : RespondsFinallyToWriteRequest;
	
	public function getBodyParserFactory() : AbstractBodyParserFactory;
}
