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

use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class ReadRouteRedirect
 * @package IceHawk\IceHawk\Routing
 */
final class RouteRedirect implements RedirectsRoute
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var  string */
	private $finalUri;

	/** @var  string */
	private $finalMethod;

	/** @var array */
	private $uriParams = [];

	public function __construct( ProvidesMatchResult $pattern, string $finalUri, string $finalMethod )
	{
		$this->pattern     = $pattern;
		$this->finalUri    = $finalUri;
		$this->finalMethod = $finalMethod;
	}

	public function matches( string $uri ) : bool
	{
		if ( $this->pattern->matches( $uri ) )
		{
			$this->uriParams = $this->pattern->getMatches();

			return true;
		}

		return false;
	}

	public function getFinalUri() : string
	{
		return $this->finalUri;
	}

	public function getFinalMethod() : string
	{
		return $this->finalMethod;
	}

	/**
	 * @return array
	 */
	public function getUriParams() : array
	{
		return $this->uriParams;
	}
}
