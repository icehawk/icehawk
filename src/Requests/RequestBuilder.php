<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod;
use Fortuneglobe\IceHawk\Interfaces\BuildsRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesHandlerDemand;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Class RequestBuilder
 * @package Fortuneglobe\IceHawk\Requests
 */
final class RequestBuilder implements BuildsRequest
{

	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesHandlerDemand */
	private $uriComponents;

	/**
	 * @param ProvidesRequestInfo   $requestInfo
	 * @param ProvidesHandlerDemand $uriComponents
	 */
	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesHandlerDemand $uriComponents )
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
	 * @return ProvidesRequestData|ProvidesReadRequestData|ProvidesWriteRequestData
	 */
	public function build( array $getData, array $postData, array $uploadedFiles ) : ProvidesRequestData
	{
		switch ( $this->requestInfo->getMethod() )
		{
			case HttpMethod::POST:
			{
				$mergedPostData = $this->getMergedData( $postData );
				$postRequest    = new WriteRequest( $this->requestInfo, $mergedPostData, $uploadedFiles );

				return $postRequest;
			}

			case HttpMethod::GET:
			case HttpMethod::HEAD:
			{
				$mergedGetData = $this->getMergedData( $getData );

				$getRequest = new ReadRequest( $this->requestInfo, $mergedGetData );

				return $getRequest;
			}

			default:
				throw ( new InvalidRequestMethod() )->withRequestMethod( $this->requestInfo->getMethod() );
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