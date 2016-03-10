<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultFinalReadRequestResponding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultFinalWriteRequestResponding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultReadRequestResolving;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultRequestInfoProviding;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultUriRewriting;
use Fortuneglobe\IceHawk\Defaults\Traits\DefaultWriteRequestResolving;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;

/**
 * Class IceHawkConfig
 * @package Fortuneglobe\IceHawk
 */
class IceHawkConfig implements ConfiguresIceHawk
{
	use DefaultUriRewriting;
	use DefaultReadRequestResolving;
	use DefaultWriteRequestResolving;
	use DefaultEventSubscribing;
	use DefaultRequestInfoProviding;
	use DefaultFinalReadRequestResponding;
	use DefaultFinalWriteRequestResponding;
}
