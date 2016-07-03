<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use IceHawk\IceHawk\Defaults\Traits\DefaultFinalReadResponding;
use IceHawk\IceHawk\Defaults\Traits\DefaultFinalWriteResponding;
use IceHawk\IceHawk\Defaults\Traits\DefaultReadRouting;
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
	use DefaultEventSubscribing;
	use DefaultRequestInfoProviding;
	use DefaultFinalReadResponding;
	use DefaultFinalWriteResponding;
}
