<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Responses\AbstractHttpResponse;

/**
 * Class SimpleRespond
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class SimpleResponse extends AbstractHttpResponse
{
	private $responseMessage;
	
	public function __construct( $responseMessage = '' )
	{
		parent::__construct( 'text/plain' );
		
		$this->responseMessage = $responseMessage;
	}
	
	protected function getBody() : string 
	{
		return $this->responseMessage;
	}
}