<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\InvalidApiCalled;
use Fortuneglobe\IceHawk\Interfaces\ServesApiData;

/**
 * Class Api
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class Api implements ServesApiData
{

	const JSON            = 'json';

	const WEB             = 'web';

	const COMMON          = self::WEB;

	const ALL             = '_all_';

	const VERSION_DEFAULT = '1.0';

	/** @var string */
	private $version;

	/**
	 * @param string $version
	 */
	public function __construct( $version )
	{
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	abstract public function getName();

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getName();
	}

	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->getName() . '-v' . $this->getVersion();
	}

	/**
	 * @return array
	 */
	public static function getAll()
	{
		return [
			self::JSON,
			self::WEB
		];
	}

	/**
	 * @param string $api
	 * @param string $version
	 *
	 * @throws InvalidApiCalled
	 * @return Apis\Json|Apis\Web|ServesApiData
	 */
	public static function factory( $api, $version = self::VERSION_DEFAULT )
	{
		switch ( $api )
		{
			case self::JSON:
				return new Apis\Json( $version );
				break;

			case self::WEB:
				return new Apis\Web( $version );
				break;

			default:
				throw new InvalidApiCalled( $api );
		}
	}
}
