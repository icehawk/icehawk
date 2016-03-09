<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Constants;

/**
 * Class Http
 * @package Fortuneglobe\IceHawk\Constants
 */
abstract class HttpMethod
{
	const OPTIONS       = 'OPTIONS';

	const HEAD          = 'HEAD';

	const GET           = 'GET';

	const POST          = 'POST';

	const PUT           = 'PUT';

	const PATCH         = 'PATCH';

	const DELETE        = 'DELETE';

	const ALL_METHODS   = [
		self::OPTIONS,
		self::HEAD,
		self::GET,
		self::POST,
		self::PUT,
		self::PATCH,
		self::DELETE,
	];

	const READ_METHODS  = [
		self::HEAD,
		self::GET,
	];

	const WRITE_METHODS = [
		self::POST,
		self::PUT,
		self::PATCH,
		self::DELETE,
	];
}