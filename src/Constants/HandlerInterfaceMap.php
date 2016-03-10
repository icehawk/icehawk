<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Constants;

use Fortuneglobe\IceHawk\Interfaces\HandlesDeleteRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesHeadRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPatchRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPutRequest;

/**
 * Class HandlerInterfaceMap
 * @package Fortuneglobe\IceHawk\Constants
 */
abstract class HandlerInterfaceMap
{
	const HTTP_METHODS = [
		HttpMethod::HEAD   => HandlesHeadRequest::class,
		HttpMethod::GET    => HandlesGetRequest::class,
		HttpMethod::POST   => HandlesPostRequest::class,
		HttpMethod::PUT    => HandlesPutRequest::class,
		HttpMethod::PATCH  => HandlesPatchRequest::class,
		HttpMethod::DELETE => HandlesDeleteRequest::class,
	];
}