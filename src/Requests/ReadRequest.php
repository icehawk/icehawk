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

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestInputData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class GetRequest
 * @package IceHawk\IceHawk\Requests
 */
final class ReadRequest implements ProvidesReadRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesReadRequestInputData */
	private $inputData;

	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesReadRequestInputData $inputData )
	{
		$this->requestInfo = $requestInfo;
		$this->inputData   = $inputData;
	}

	public function getInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInput() : ProvidesReadRequestInputData
	{
		return $this->inputData;
	}
}
