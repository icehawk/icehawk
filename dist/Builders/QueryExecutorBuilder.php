<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\DomainDemandBuilder;
use Fortuneglobe\IceHawk\Exceptions\DomainQueryExecutorNotFound;
use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainQueries;

/**
 * Class QueryExecutorBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class QueryExecutorBuilder extends DomainDemandBuilder
{
	/**
	 * @throws DomainQueryExecutorNotFound
	 * @return ExecutesDomainQueries
	 */
	public function buildQueryExecutor()
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\QueryExecutors';
		$class_name = $namespace . '\\' . $this->getActionCamelCase() . 'Executor';

		if ( class_exists( $class_name ) )
		{
			return new $class_name();
		}
		else
		{
			throw new DomainQueryExecutorNotFound( $class_name . ' not found.' );
		}
	}
}