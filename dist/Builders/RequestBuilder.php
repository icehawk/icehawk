<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod;
use Fortuneglobe\IceHawk\Interfaces\BuildsRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;

/**
 * Class RequestBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class RequestBuilder implements BuildsRequests
{

	/** @var ServesRequestInfo */
	private $requestInfo;

	/**
	 * @param ServesRequestInfo $requestInfo
	 */
	public function __construct( ServesRequestInfo $requestInfo )
	{
		$this->requestInfo = $requestInfo;
	}

	/**
	 * @param array $getData
	 * @param array $postData
	 * @param array $uploadedFiles
	 *
	 * @throws InvalidRequestMethod
	 * @return ServesGetRequestData|ServesPostRequestData
	 */
	public function buildRequest( array $getData, array $postData, array $uploadedFiles )
	{
		if ( $this->requestInfo->getMethod() == Http::METHOD_POST )
		{
			return new PostRequest( $postData, $uploadedFiles );
		}
		elseif ( $this->requestInfo->getMethod() == Http::METHOD_GET )
		{
			return new GetRequest( $getData );
		}
		else
		{
			throw new InvalidRequestMethod( $this->requestInfo->getMethod() );
		}
	}
}