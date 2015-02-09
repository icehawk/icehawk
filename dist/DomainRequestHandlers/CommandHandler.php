<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\Api;
use Fortuneglobe\IceHawk\Builders\CommandBuilder;
use Fortuneglobe\IceHawk\Builders\CommandExecutorBuilder;
use Fortuneglobe\IceHawk\DomainCommand;
use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\DomainCommandExecutorNotFound;
use Fortuneglobe\IceHawk\Exceptions\DomainCommandNotFound;
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainCommand;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainCommands;
use Fortuneglobe\IceHawk\Interfaces\ServesWriteRequestData;
use Fortuneglobe\IceHawk\Responder;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;
use Fortuneglobe\IceHawk\Responses\Forbidden;
use Fortuneglobe\IceHawk\Responses\NotFound;

/**
 * Class CommandHandler
 *
 * @package Fortuneglobe\IceHawk\DomainRequestHandlers
 */
final class CommandHandler extends DomainRequestHandler
{

	/**
	 * @param ServesWriteRequestData $request
	 *
	 * @throws InvalidRequestType
	 * @throws InvalidDomainCommand
	 */
	public function handleRequest( ServesWriteRequestData $request )
	{
		$responder = new Responder( $this->api );

		try
		{
			$command = $this->buildCommandByRequest( $request );
			$this->validateAndExecuteCommand( $command, $responder );
		}
		catch ( DomainCommandNotFound $e )
		{
			$responder->add( Api::ALL, new NotFound() );
		}

		$responder->respond();
	}

	/**
	 * @param ServesWriteRequestData $request
	 *
	 * @throws \Fortuneglobe\IceHawk\Exceptions\DomainCommandNotFound
	 * @return \Fortuneglobe\IceHawk\DomainCommand
	 */
	private function buildCommandByRequest( ServesWriteRequestData $request )
	{
		$builder = new CommandBuilder( $this->domain, $this->demand, $this->project_namespace );

		return $builder->buildCommand( $request );
	}

	/**
	 * @param DomainCommand $command
	 * @param Responder     $responder
	 */
	private function validateAndExecuteCommand( DomainCommand $command, Responder $responder )
	{
		if ( $command->isExecutable() )
		{
			if ( $command->isValid() )
			{
				$this->executeCommand( $command, $responder );
			}
			else
			{
				$responder->add( Api::WEB, new BadRequest( $command->getValidationMessages() ) );
				$responder->add( Api::JSON, new BadJsonRequest( $command->getValidationMessages() ) );
			}
		}
		else
		{
			$responder->add( Api::ALL, new Forbidden() );
		}
	}

	/**
	 * @param DomainCommand $command
	 * @param Responder     $responder
	 */
	private function executeCommand( DomainCommand $command, Responder $responder )
	{
		try
		{
			$executor = $this->buildCommandExecutor();
			$executor->execute( $command, $responder );
		}
		catch ( DomainCommandExecutorNotFound $e )
		{
			$responder->add( Api::WEB, new BadRequest( [ $e->getMessage() ] ) );
			$responder->add( Api::JSON, new BadJsonRequest( [ $e->getMessage() ] ) );
		}
	}

	/**
	 * @throws DomainCommandExecutorNotFound
	 * @return ExecutesDomainCommands
	 */
	private function buildCommandExecutor()
	{
		$builder = new CommandExecutorBuilder( $this->domain, $this->demand, $this->project_namespace );

		return $builder->buildCommandExecutor();
	}
}
