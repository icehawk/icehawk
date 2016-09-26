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

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;
use IceHawk\IceHawk\PubSub\Interfaces\PublishesEvents;

/**
 * Class AbstractRequestHandler
 * @package IceHawk\IceHawk\RequestHandlers
 */
abstract class AbstractRequestHandler
{
	/** @var ConfiguresIceHawk */
	protected $config;

	/** @var PublishesEvents */
	private $eventPublisher;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param PublishesEvents   $eventPublisher
	 */
	final public function __construct( ConfiguresIceHawk $config, PublishesEvents $eventPublisher )
	{
		$this->config         = $config;
		$this->eventPublisher = $eventPublisher;
	}

	final protected function publishEvent( CarriesEventData $event )
	{
		$this->eventPublisher->publish( $event );
	}

	abstract public function handleRequest();
}
