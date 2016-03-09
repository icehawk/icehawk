<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\RequestHandlers\Interfaces\HandlesWriteRequest;

/**
 * Class WriteRequestHandler
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
abstract class WriteRequestHandler extends AbstractRequestHandler implements HandlesWriteRequest
{
	protected function getRequestContract() : string
	{
		return ProvidesWriteRequestData::class;
	}

	final public function handleRequest()
	{
		/** @var ProvidesWriteRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}
