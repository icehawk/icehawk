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

namespace IceHawk\IceHawk\Exceptions;

/**
 * Class InvalidEventSubscriberCollection
 * @package IceHawk\IceHawk\Exceptions
 */
final class InvalidEventSubscriberCollection extends IceHawkException
{
	/** @var array */
	private $invalidKeys = [];

	/**
	 * @return array
	 */
	public function getInvalidKeys() : array
	{
		return $this->invalidKeys;
	}

	/**
	 * @param array $invalidKeys
	 *
	 * @return InvalidEventSubscriberCollection
	 */
	public function withInvalidKeys( array $invalidKeys ) : self
	{
		$this->invalidKeys = $invalidKeys;

		return $this;
	}
}
