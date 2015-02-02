<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainCommandBuilders;

use Fortuneglobe\IceHawk\DomainCommand;
use Fortuneglobe\IceHawk\DomainCommandBuilder;
use Fortuneglobe\IceHawk\Exceptions\DomainCommandNotFound;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class CommandBuilder
 *
 * @package Fortuneglobe\IceHawk\DomainCommandBuilders
 */
final class CommandBuilder extends DomainCommandBuilder
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @throws DomainCommandNotFound
	 * @return DomainCommand
	 */
	public function buildCommand( ServesRequestData $request )
	{
		$namespace  = $this->getProjectNamespace() . '\\' . $this->getDomainCamelCase() . '\\Commands';
		$class_name = $namespace . '\\' . $this->getCommandCamelCase();

		if ( class_exists( $class_name ) )
		{
			return new $class_name( $this->api, $this->domain, $request );
		}
		else
		{
			throw new DomainCommandNotFound( $class_name );
		}
	}
}
