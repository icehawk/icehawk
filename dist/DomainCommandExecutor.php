<?php
/**
 *
 * @author hollodotme
 */

namespace Dreiwolt\Backlog;

use Fortuneglobe\IceHawk\Api;
use Fortuneglobe\IceHawk\DomainCommand;
use Fortuneglobe\IceHawk\Interfaces\ExecutesDomainCommands;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Responses\BadJsonRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class DomainCommandExecutor
 *
 * @package Dreiwolt\Backlog
 */
abstract class DomainCommandExecutor implements ExecutesDomainCommands
{

	/** @var DomainCommand */
	private $command;

	/**
	 * @param DomainCommand $command
	 */
	final public function __construct( DomainCommand $command )
	{
		$this->command = $command;
	}

	public function execute()
	{
		$this->handleExecution( $this->command );
	}

	/**
	 * @param DomainCommand $command
	 */
	abstract protected function handleExecution( $command );

	/**
	 * @param string         $api_name
	 * @param ServesResponse $response
	 * @param string         $api_version
	 */
	protected function addResponse( $api_name, ServesResponse $response, $api_version = Api::VERSION_DEFAULT )
	{
		$responder = $this->command->getResponder();

		if ( $api_name == Api::WEB && ($response instanceof Redirect) && $this->command->hasSuccessUrl() )
		{
			$responder->add( $api_name, new Redirect( $this->command->getSuccessUrl() ), $api_version );
		}
		else
		{
			$responder->add( $api_name, $response, $api_version );
		}
	}

	/**
	 * @param array $messages
	 */
	protected function addBadRequest( array $messages )
	{
		$this->addBadWebRequest( $messages );
		$this->addBadJsonRequest( $messages );
	}

	/**
	 * @param array $messages
	 */
	private function addBadWebRequest( array $messages )
	{
		$responder = $this->command->getResponder();

		if ( $this->command->hasFailUrl() )
		{
			$responder->add( Api::WEB, new Redirect( $this->command->getFailUrl() ) );
		}
		else
		{
			$responder->add( Api::WEB, new BadRequest( $messages ) );
		}
	}

	/**
	 * @param array $messages
	 */
	private function addBadJsonRequest( array $messages )
	{
		$responder = $this->command->getResponder();

		$responder->add( Api::JSON, new BadJsonRequest( $messages ) );
	}
}
