<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\DomainDemandBuilder;
use Fortuneglobe\IceHawk\Exceptions\DomainCommandExecutorNotFound;
use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainCommands;

/**
 * Class CommandExecutorBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class CommandExecutorBuilder extends DomainDemandBuilder
{
	/**
	 * @throws DomainCommandExecutorNotFound
	 * @return ExecutesDomainCommands
	 */
	public function buildCommandExecutor()
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\CommandExecutors';
		$class_name = $namespace . '\\' . $this->getActionCamelCase() . 'Executor';

		if ( class_exists( $class_name ) )
		{
			return new $class_name();
		}
		else
		{
			throw new DomainCommandExecutorNotFound( $class_name . ' not found.' );
		}
	}
}