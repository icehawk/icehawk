<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\RequestParsers\AbstractBodyParserFactory;

/**
 * Class IceHawkConfigWrapper
 * @package Fortuneglobe\IceHawk\Config
 */
final class ConfigWrapper implements ConfiguresIceHawk
{
	/** @var RewritesUri */
	private $uriRewriter;

	/** @var ResolvesReadRequest */
	private $readRequestResolver;

	/** @var ResolvesWriteRequest */
	private $writeRequestResolver;

	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var array|SubscribesToEvents[] */
	private $eventSubscribers;

	/** @var RespondsFinallyToReadRequest */
	private $finalReadRequestResponder;

	/** @var RespondsFinallyToWriteRequest */
	private $finalWriteRequestResponder;

	/** @var AbstractBodyParserFactory */
	private $bodyParserFactory;

	public function __construct( ConfiguresIceHawk $config )
	{
		$this->uriRewriter                = $config->getUriRewriter();
		$this->readRequestResolver        = $config->getReadRequestResolver();
		$this->writeRequestResolver       = $config->getWriteRequestResolver();
		$this->eventSubscribers           = $config->getEventSubscribers();
		$this->requestInfo                = $config->getRequestInfo();
		$this->finalReadRequestResponder  = $config->getFinalReadRequestResponder();
		$this->finalWriteRequestResponder = $config->getFinalWriteRequestResponder();
		$this->bodyParserFactory          = $config->getBodyParserFactory();
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getReadRequestResolver() : ResolvesReadRequest
	{
		return $this->readRequestResolver;
	}

	public function getWriteRequestResolver() : ResolvesWriteRequest
	{
		return $this->writeRequestResolver;
	}

	public function getUriRewriter() : RewritesUri
	{
		return $this->uriRewriter;
	}

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array
	{
		return $this->eventSubscribers;
	}

	public function getFinalReadRequestResponder() : RespondsFinallyToReadRequest
	{
		return $this->finalReadRequestResponder;
	}

	public function getFinalWriteRequestResponder() : RespondsFinallyToWriteRequest
	{
		return $this->finalWriteRequestResponder;
	}
	
	public function getBodyParserFactory() : AbstractBodyParserFactory
	{
		return $this->bodyParserFactory;
	}
}