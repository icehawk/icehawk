<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\Api;
use Fortuneglobe\IceHawk\DomainCommandBuilders\QueryBuilder;
use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainQuery;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;

/**
 * Class QueryHandler
 *
 * @package Fortuneglobe\IceHawk\DomainRequestHandlers
 */
final class QueryHandler extends DomainRequestHandler
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @throws InvalidRequestType
	 * @throws InvalidDomainQuery
	 */
	public function handleRequest( ServesRequestData $request )
	{
		$this->guardRequestType( $request );

		$query     = $this->buildQueryByRequest( $request );
		$responder = $query->getResponder();

		if ( $query->isExecutable() )
		{
			if ( $query->isValid() )
			{
				$query->execute();
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

		$responder->respond();
	}

	/**
	 * @param ServesRequestData $request
	 *
	 * @throws InvalidRequestType
	 */
	private function guardRequestType( ServesRequestData $request )
	{
		if ( !($request instanceof GetRequest) )
		{
			throw new InvalidRequestType( get_class( $request ) );
		}
	}

	/**
	 * @param ServesRequestData $request
	 *
	 * @return \Fortuneglobe\IceHawk\DomainQuery
	 * @throws \Fortuneglobe\IceHawk\Exceptions\DomainQueryNotFound
	 */
	private function buildQueryByRequest( ServesRequestData $request )
	{
		$builder = new QueryBuilder( $this->api, $this->domain, $this->command, $this->project_namespace );

		return $builder->buildCommand( $request );
	}
}
