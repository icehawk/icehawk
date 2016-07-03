<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Constants;

use IceHawk\IceHawk\Interfaces\HandlesDeleteRequest;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\HandlesHeadRequest;
use IceHawk\IceHawk\Interfaces\HandlesPatchRequest;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\HandlesPutRequest;

/**
 * Class HandlerMethodInterfaceMap
 * @package IceHawk\IceHawk\Constants
 */
abstract class HandlerMethodInterfaceMap
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