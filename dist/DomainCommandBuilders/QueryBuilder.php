<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainCommandBuilders;

use Fortuneglobe\IceHawk\DomainCommandBuilder;
use Fortuneglobe\IceHawk\DomainQuery;
use Fortuneglobe\IceHawk\Exceptions\DomainQueryNotFound;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class QueryBuilder
 *
 * @package Fortuneglobe\IceHawk\DomainCommandBuilders
 */
final class QueryBuilder extends DomainCommandBuilder
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @throws DomainQueryNotFound
	 * @return DomainQuery
	 */
	public function buildCommand( ServesRequestData $request )
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\Queries';
		$class_name = $namespace . '\\' . $this->getCommandCamelCase();

		if ( class_exists( $class_name ) )
		{
			return new $class_name( $this->api, $this->domain, $request );
		}
		else
		{
			throw new DomainQueryNotFound( $class_name );
		}
	}
}
