<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\ResponderIsMissingAResponseForApi;
use Fortuneglobe\IceHawk\Interfaces\ServesApiData;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;

/**
 * Class Responder
 *
 * @package Fortuneglobe\IceHawk
 */
class Responder implements ServesResponse
{

	/** @var ServesApiData */
	private $api;

	/** @var array|ServesResponse[] */
	private $responses;

	/**
	 * @param ServesApiData $api
	 */
	public function __construct( ServesApiData $api )
	{
		$this->api       = $api;
		$this->responses = [ ];
	}

	/**
	 * @param string         $api_name
	 * @param ServesResponse $response
	 * @param string         $api_version
	 *
	 * @throws Exceptions\InvalidApiCalled
	 */
	public function add( $api_name, ServesResponse $response, $api_version = Api::VERSION_DEFAULT )
	{
		if ( $api_name == Api::ALL )
		{
			foreach ( Api::getAll() as $name )
			{
				$api = Api::factory( $name, $api_version );
				$this->addResponse( $api, $response );
			}
		}
		else
		{
			$api = Api::factory( $api_name, $api_version );
			$this->addResponse( $api, $response );
		}
	}

	/**
	 * @param ServesApiData  $api
	 * @param ServesResponse $response
	 */
	private function addResponse( ServesApiData $api, ServesResponse $response )
	{
		$this->responses[ $api->getIdentifier() ] = $response;
	}

	/**
	 * @throws ResponderIsMissingAResponseForApi
	 */
	public function respond()
	{
		if ( isset($this->responses[ $this->api->getIdentifier() ]) )
		{
			$response = $this->responses[ $this->api->getIdentifier() ];
			$response->respond();
		}
		else
		{
			throw new ResponderIsMissingAResponseForApi( $this->api->getIdentifier() );
		}
	}
}
