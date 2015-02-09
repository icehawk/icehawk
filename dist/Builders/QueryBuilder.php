<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\DomainDemandBuilder;
use Fortuneglobe\IceHawk\DomainQuery;
use Fortuneglobe\IceHawk\Exceptions\DomainQueryNotFound;
use Fortuneglobe\IceHawk\Interfaces\ServesReadRequestData;

/**
 * Class QueryBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class QueryBuilder extends DomainDemandBuilder
{
	/**
	 * @param ServesReadRequestData $request
	 *
	 * @throws DomainQueryNotFound
	 * @return DomainQuery
	 */
	public function buildQuery( ServesReadRequestData $request )
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\Queries';
		$class_name = $namespace . '\\' . $this->getActionCamelCase();

		if ( class_exists( $class_name ) )
		{
			return new $class_name( $this->domain, $request );
		}
		else
		{
			throw new DomainQueryNotFound( $class_name );
		}
	}
}
