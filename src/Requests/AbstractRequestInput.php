<?php
namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInputData;

/**
 * Class AbstractRequestInput
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
abstract class AbstractRequestInput implements ProvidesRequestInputData
{
	/**
	 * @var array
	 */
	protected $uriParams;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * AbstractRequestInput constructor.
	 *
	 * @param array $uriParams
	 */
	public function __construct( array $uriParams )
	{
		$this->uriParams = $uriParams;
	}

	public function getData() : array
	{
		if ( is_null( $this->data ) )
		{
			$this->data = $this->getMergedRequestData();
		}

		return $this->data;
	}
	
	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key )
	{
		return $this->getData()[ $key ] ?? null;
	}
	
	abstract protected function getMergedRequestData() : array;
}