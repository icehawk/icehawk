<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\DomainCommand;
use Fortuneglobe\IceHawk\DomainDemandBuilder;
use Fortuneglobe\IceHawk\Exceptions\DomainCommandNotFound;
use Fortuneglobe\IceHawk\Interfaces\ServesWriteRequestData;

/**
 * Class CommandBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class CommandBuilder extends DomainDemandBuilder
{
	/**
	 * @param ServesWriteRequestData $request
	 *
	 * @throws DomainCommandNotFound
	 * @return DomainCommand
	 */
	public function buildCommand( ServesWriteRequestData $request )
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\Commands';
		$class_name = $namespace . '\\' . $this->getActionCamelCase();

		if ( class_exists( $class_name ) )
		{
			return new $class_name( $this->domain, $request );
		}
		else
		{
			throw new DomainCommandNotFound( $class_name );
		}
	}
}
