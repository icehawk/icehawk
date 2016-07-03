<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultFinalReadResponding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultFinalWriteResponding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultReadRouting;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultRequestInfoProviding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultWriteRouting;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;

/**
 * Class IceHawkConfig
 * @package Fortuneglobe\IceHawk
 */
class IceHawkConfig implements ConfiguresIceHawk
{
	use DefaultReadRouting;
	use DefaultWriteRouting;
	use DefaultEventSubscribing;
	use DefaultRequestInfoProviding;
	use DefaultFinalReadResponding;
	use DefaultFinalWriteResponding;
}
