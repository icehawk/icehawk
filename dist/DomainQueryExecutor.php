<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainQueries;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;

/**
 * Class DomainQueryExecutor
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainQueryExecutor implements ExecutesDomainQueries
{

	/** @var DomainQuery */
	private $query;

	/**
	 * @param DomainQuery $query
	 */
	final public function __construct( DomainQuery $query )
	{
		$this->query = $query;
	}

	public function execute()
	{
		$this->handleExecution( $this->query );
	}

	/**
	 * @param DomainQuery $query
	 */
	abstract protected function handleExecution( $query );

	/**
	 * @param string         $api_name
	 * @param ServesResponse $response
	 * @param string         $api_version
	 */
	protected function addResponse( $api_name, ServesResponse $response, $api_version = Api::VERSION_DEFAULT )
	{
		$responder = $this->query->getResponder();
		$responder->add( $api_name, $response, $api_version );
	}

	/**
	 * @param array $messages
	 */
	protected function addBadRequest( array $messages )
	{
		$responder = $this->query->getResponder();

		$responder->add( Api::WEB, new BadRequest( $messages ) );
		$responder->add( Api::JSON, new BadJsonRequest( $messages ) );
	}
}