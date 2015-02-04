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

	const METHOD_POST       = 'POST';

	const METHOD_GET        = 'GET';

	const MOVED_PERMANENTLY = 'HTTP/1.1 301 Moved Permanently';

	const MOVED_TEMPORARILY = 'HTTP/1.1 302 Moved Temporarily';

	const FORBIDDEN         = 'HTTP/1.1 403 Forbidden';

	const BAD_REQUEST = 'HTTP/1.1 400 Bad Request';

}