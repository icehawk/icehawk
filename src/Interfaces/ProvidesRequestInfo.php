<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestInfo
{
	public function isSecure() : bool;

	public function getMethod() : string;

	public function getUri() : string;

	public function getHost() : string;

	public function getUserAgent() : string;

	public function getServerAddress() : string;

	public function getClientAddress() : string;

	public function getRequestTimeFloat() : float;

	public function getAcceptedContentTypes() : string;

	public function getQueryString() : string;

	public function getReferer() : string;

	public function getAuthUser() : string;

	public function getAuthPassword() : string;

	public function getContentType() : string;

	public function getContentLength() : string;
}
