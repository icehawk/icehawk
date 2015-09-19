<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Constants;

/**
 * Class Http
 *
 * @package Fortuneglobe\IceHawk\Constants
 */
abstract class Http
{

	const METHOD_POST           = 'POST';

	const METHOD_GET            = 'GET';

	const METHOD_HEAD           = 'HEAD';

	const MOVED_PERMANENTLY     = 301;

	const MOVED_TEMPORARILY     = 302;

	const FORBIDDEN             = 403;

	const BAD_REQUEST           = 400;

	const NOT_FOUND             = 404;

	const UNAUTHORIZED          = 401;

	const METHOD_NOT_ALLOWED    = 405;

	const INTERNAL_SERVER_ERROR = 500;
}