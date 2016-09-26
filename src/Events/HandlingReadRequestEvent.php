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

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestInputData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;

/**
 * Class HandlingReadRequestEvent
 * @package IceHawk\IceHawk\Events
 */
final class HandlingReadRequestEvent implements CarriesEventData
{
	/** @var ProvidesReadRequestData */
	private $request;

	public function __construct( ProvidesReadRequestData $request )
	{
		$this->request = $request;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getInfo();
	}

	public function getRequestInput() : ProvidesReadRequestInputData
	{
		return $this->request->getInput();
	}
}
