<?php declare(strict_types = 1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
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
