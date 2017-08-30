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

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Defaults\Traits\DefaultCookieProviding;
use IceHawk\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use IceHawk\IceHawk\Defaults\Traits\DefaultFinalReadResponding;
use IceHawk\IceHawk\Defaults\Traits\DefaultFinalWriteResponding;
use IceHawk\IceHawk\Defaults\Traits\DefaultReadRouting;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestBypassing;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestInfoProviding;
use IceHawk\IceHawk\Defaults\Traits\DefaultWriteRouting;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;

/**
 * Class IceHawkConfig
 * @package IceHawk\IceHawk
 */
class IceHawkConfig implements ConfiguresIceHawk
{
	use DefaultReadRouting;
	use DefaultWriteRouting;
	use DefaultRequestBypassing;
	use DefaultEventSubscribing;
	use DefaultRequestInfoProviding;
	use DefaultCookieProviding;
	use DefaultFinalReadResponding;
	use DefaultFinalWriteResponding;
}
