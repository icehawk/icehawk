<?php
namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestInputData;

/**
 * Class ReadRequestInput
 * @package IceHawk\IceHawk\Requests
 */
final class ReadRequestInput implements ProvidesReadRequestInputData
{
	/** @var array */
	private $data;

	public function __construct( array $data )
	{
		$this->data = $data;
	}

	public function getData() : array
	{
		return $this->data;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key )
	{
		return $this->data[ $key ] ?? null;
	}
}
