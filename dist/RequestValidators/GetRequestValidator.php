<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestValidators;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\RequestValidator;

/**
 * Class GetRequestValidator
 *
 * @package Fortuneglobe\IceHawk\RequestValidators
 */
final class GetRequestValidator extends RequestValidator
{

	/** @var ServesGetRequestData */
	protected $request;

}
