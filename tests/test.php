<?php declare(strict_types = 1);
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests;

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

class Request
{

}

class ReadRequest extends Request
{

}

abstract class Test
{
	public function __construct( Request $request )
	{
		$contract = $this->getContract();

		echo ($request instanceof $contract) ? 'Yes' : 'No';
	}

	abstract protected function getContract() : string;
}

class ReadTest extends Test
{
	protected function getContract() : string
	{
		return ReadRequest::class;
	}
}

new ReadTest( new ReadRequest() );