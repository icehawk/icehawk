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

namespace IceHawk\IceHawk\Config;

use IceHawk\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfigGuard
 * @package IceHawk\IceHawk\Config
 */
final class ConfigGuard
{
	/** @var ConfiguresIceHawk */
	private $config;

	/**
	 * @param ConfiguresIceHawk $config
	 */
	public function __construct( ConfiguresIceHawk $config )
	{
		$this->config = $config;
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	public function validate()
	{
		$this->guardEventSubscribersAreValid();
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	private function guardEventSubscribersAreValid()
	{
		$eventSubscribers = $this->config->getEventSubscribers();

		$invalidSubscribers = array_filter(
			$eventSubscribers,
			function ( $subscriber )
			{
				return (!is_object( $subscriber ) || !($subscriber instanceof SubscribesToEvents));
			}
		);

		if ( !empty($invalidSubscribers) )
		{
			throw (new InvalidEventSubscriberCollection())->withInvalidKeys( array_keys( $invalidSubscribers ) );
		}
	}
}
