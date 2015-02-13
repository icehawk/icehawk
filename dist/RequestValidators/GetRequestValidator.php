<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestValidators;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\Validation\FluidValidator;

/**
 * Class GetRequestValidator
 *
 * @package Fortuneglobe\IceHawk\RequestValidators
 */
final class GetRequestValidator extends FluidValidator
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
	 * @param string $var
	 *
	 * @return array|mixed|null|string
	 */
	protected function getValue( $var )
	{
		return $this->request->get( $var );
	}
}
