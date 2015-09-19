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
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;
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

	/** @var ServesUriComponents */
	private $uriComponents;

	/**
	 * @param ServesRequestInfo   $requestInfo
	 * @param ServesUriComponents $uriComponents
	 */
	public function __construct( ServesRequestInfo $requestInfo, ServesUriComponents $uriComponents )
	{
		$this->requestInfo   = $requestInfo;
		$this->uriComponents = $uriComponents;
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
			return new PostRequest( $this->getMergedData( $postData ), $uploadedFiles );
		}
		elseif ( in_array( $this->requestInfo->getMethod(), [ Http::METHOD_GET, Http::METHOD_HEAD ] ) )
		{
			return new GetRequest( $this->getMergedData( $getData ) );
		}
		else
		{
			throw new InvalidRequestMethod( $this->requestInfo->getMethod() );
		}
	}

	/**
	 * @param array $requestData
	 *
	 * @return array
	 */
	private function getMergedData( array $requestData )
	{
		return array_merge( $requestData, $this->uriComponents->getParams() );
	}
}