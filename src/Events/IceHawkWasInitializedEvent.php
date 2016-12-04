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

namespace IceHawk\IceHawk\Events;

use IceHawk\IceHawk\Interfaces\ProvidesCookieData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class IceHawkWasInitializedEvent
 * @package IceHawk\IceHawk\Events
 */
final class IceHawkWasInitializedEvent implements CarriesEventData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesCookieData */
	private $requestCookies;

	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesCookieData $requestCookies )
	{
		$this->requestInfo    = $requestInfo;
		$this->requestCookies = $requestCookies;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getRequestCookies() : ProvidesCookieData
	{
		return $this->requestCookies;
	}
}
