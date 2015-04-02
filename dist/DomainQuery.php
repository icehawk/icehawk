<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;

/**
 * Class DomainQuery
 *
 * @package Dreiwolt\Backlog
 */
abstract class DomainQuery
{

	/** @var ServesGetRequestData */
	protected $request;

	/**
	 * @param ServesGetRequestData $request
	 */
	public function __construct( ServesGetRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( $key )
	{
		return $this->request->get( $key );
	}
}
