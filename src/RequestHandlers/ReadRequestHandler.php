<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\RequestHandlers\Interfaces\HandlesReadRequest;

/**
 * Class ReadRequestHandler
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
abstract class ReadRequestHandler extends AbstractRequestHandler implements HandlesReadRequest
{
	final protected function getRequestContract() : string
	{
		return ProvidesReadRequestData::class;
	}

	final public function handleRequest()
	{
		/** @var ProvidesReadRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}
