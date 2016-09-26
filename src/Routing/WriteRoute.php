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

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Interfaces\HandlesWriteRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class WriteRoute
 * @package IceHawk\IceHawk\Routing
 */
final class WriteRoute implements RoutesToWriteHandler
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var HandlesWriteRequest */
	private $requestHandler;

	public function __construct( ProvidesMatchResult $pattern, HandlesWriteRequest $requestHandler )
	{
		$this->pattern        = $pattern;
		$this->requestHandler = $requestHandler;
	}

	public function matches( string $uri ) : bool
	{
		return $this->pattern->matches( $uri );
	}

	public function getUriParams() : array
	{
		return $this->pattern->getMatches();
	}

	public function getRequestHandler() : HandlesWriteRequest
	{
		return $this->requestHandler;
	}
}
