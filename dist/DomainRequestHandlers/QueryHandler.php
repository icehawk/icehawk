<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\Api;
use Fortuneglobe\IceHawk\Builders\QueryBuilder;
use Fortuneglobe\IceHawk\Builders\QueryExecutorBuilder;
use Fortuneglobe\IceHawk\DomainQuery;
use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\DomainQueryExecutorNotFound;
use Fortuneglobe\IceHawk\Exceptions\DomainQueryNotFound;
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainQuery;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainQueries;
use Fortuneglobe\IceHawk\Interfaces\ServesReadRequestData;
use Fortuneglobe\IceHawk\Responder;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;
use Fortuneglobe\IceHawk\Responses\NotFound;

/**
 * Class QueryHandler
 *
 * @package Fortuneglobe\IceHawk\DomainRequestHandlers
 */
final class QueryHandler extends DomainRequestHandler
{
	/**
	 * @param ServesReadRequestData $request
	 *
	 * @throws InvalidRequestType
	 * @throws InvalidDomainQuery
	 */
	public function handleRequest( ServesReadRequestData $request )
	{
		$responder = new Responder( $this->api );

		try
		{
			$query = $this->buildQueryByRequest( $request );
			$this->validateAndExecuteQuery( $query, $responder );
		}
		catch ( DomainQueryNotFound $e )
		{
			$responder->add( Api::ALL, new NotFound() );
		}

		$responder->respond();
	}

	/**
	 * @param ServesReadRequestData $request
	 *
	 * @throws \Fortuneglobe\IceHawk\Exceptions\DomainQueryNotFound
	 * @return \Fortuneglobe\IceHawk\DomainQuery
	 */
	private function buildQueryByRequest( ServesReadRequestData $request )
	{
		$builder = new QueryBuilder( $this->domain, $this->demand, $this->project_namespace );

		return $builder->buildQuery( $request );
	}

	/**
	 * @param DomainQuery $query
	 * @param Responder   $responder
	 */
	private function validateAndExecuteQuery( DomainQuery $query, Responder $responder )
	{
		if ( $query->isExecutable() )
		{
			if ( $query->isValid() )
			{
				$this->executeQuery( $query, $responder );
			}
			else
			{
				$responder->add( Api::WEB, new BadRequest( $query->getValidationMessages() ) );
				$responder->add( Api::JSON, new BadJsonRequest( $query->getValidationMessages() ) );
			}
		}
		else
		{
			$responder->add( Api::WEB, new BadRequest( [ "Query is not executable." ] ) );
			$responder->add( Api::JSON, new BadJsonRequest( [ "Query is not executable." ] ) );
		}
	}

	/**
	 * @param DomainQuery $query
	 * @param Responder   $responder
	 */
	private function executeQuery( DomainQuery $query, Responder $responder )
	{
		try
		{
			$executor = $this->buildQueryExecutor();
			$executor->execute( $query, $responder );
		}
		catch ( DomainQueryExecutorNotFound $e )
		{
			$responder->add( Api::WEB, new BadRequest( [ $e->getMessage() ] ) );
			$responder->add( Api::JSON, new BadJsonRequest( [ $e->getMessage() ] ) );
		}
	}

	/**
	 * @throws \Fortuneglobe\IceHawk\Exceptions\DomainQueryExecutorNotFound
	 * @return ExecutesDomainQueries
	 */
	private function buildQueryExecutor()
	{
		$builder = new QueryExecutorBuilder( $this->domain, $this->demand, $this->project_namespace );

		return $builder->buildQueryExecutor();
	}
}
