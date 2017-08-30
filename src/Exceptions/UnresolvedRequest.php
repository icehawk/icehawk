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

namespace IceHawk\IceHawk\Exceptions;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;

/**
 * Class UnresolvedRequest
 * @package IceHawk\IceHawk\Exceptions
 */
final class UnresolvedRequest extends IceHawkException
{
	/** @var ProvidesDestinationInfo */
	private $destinationInfo = '';

	public function withDestinationInfo( ProvidesDestinationInfo $destinationInfo ) : self
	{
		$this->destinationInfo = $destinationInfo;

		return $this;
	}

	public function getDestinationInfo() : ProvidesDestinationInfo
	{
		return $this->destinationInfo;
	}
}
