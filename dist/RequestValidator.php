<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\Validation\FluidValidator;

/**
 * Class RequestValidator
 *
 * @package Fortuneglobe\IceHawk
 */
class RequestValidator extends FluidValidator
{

	/** @var ServesRequestData */
	protected $request;

	/**
	 * @param ServesRequestData $request
	 */
	public function __construct( ServesRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $var
	 *
	 * @return array|mixed|null|string
	 */
	protected function getValue( $var )
	{
		return $this->request->get( $var );
	}
}
