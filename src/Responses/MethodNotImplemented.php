<?php declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Responses;

use IceHawk\IceHawk\Constants\HttpCode;

/**
 * Class MethodNotImplemented
 * @package IceHawk\IceHawk\Responses
 */
final class MethodNotImplemented extends AbstractHttpResponse
{
	/** @var string */
	private $requestMethod;

	public function __construct( string $requestMethod )
	{
		parent::__construct( 'text/plain', HttpCode::NOT_IMPLEMENTED );

		$this->requestMethod = $requestMethod;
	}

	protected function getBody() : string
	{
		return sprintf( '%d - Method Not Implemented (%s)', $this->getHttpCode(), $this->requestMethod );
	}
}
