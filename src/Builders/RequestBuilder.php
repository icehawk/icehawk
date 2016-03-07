<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\Constants\HttpMethod;
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
		if ( $this->requestInfo->getMethod() == HttpMethod::POST )
		{
			$mergedPostData = $this->getMergedData( $postData );
			$postRequest    = new PostRequest( $this->requestInfo, $mergedPostData, $uploadedFiles );

			return $postRequest;
		}
		elseif ( in_array( $this->requestInfo->getMethod(), [ HttpMethod::GET, HttpMethod::HEAD ] ) )
		{
			$mergedGetData = $this->getMergedData( $getData );

			$getRequest = new GetRequest( $this->requestInfo, $mergedGetData );

			return $getRequest;
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