<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\DomainCommandBuilders\QueryBuilder;
use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainQuery;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Requests\GetRequest;

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

		$query = $this->buildQueryByRequest( $request );

		if ( $query->isExecutable() )
		{
			if ( $query->isValid() )
			{
				$query->execute();
			}
			else
			{
				$query->getResponder()->addBadRequest( $query->getValidationMessages() );
			}
		}
		else
		{
			$query->getResponder()->addBadRequest( [ "Query is not executable." ] );
		}

		$query->getResponder()->respond();
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
