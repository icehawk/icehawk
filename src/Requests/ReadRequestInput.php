<?php
namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestInputData;

/**
 * Class ReadRequestInput
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
class ReadRequestInput extends AbstractRequestInput implements ProvidesReadRequestInputData
{
	protected function getMergedRequestData() : array
	{
		return array_merge( $_GET, $this->uriParams );
	}
}