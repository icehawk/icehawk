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

namespace IceHawk\IceHawk\Constants;

/**
 * Class HttpCode
 * @package IceHawk\IceHawk\Constants
 */
abstract class HttpCode
{
	const CONTINUE                             = 100;

	const SWITCHING_PROTOCOLS                  = 101;

	const PROCESSING                           = 102;

	const OK                                   = 200;

	const CREATED                              = 201;

	const ACCEPTED                             = 202;

	const NON_AUTHORITATIVE_INFORMATION        = 203;

	const NO_CONTENT                           = 204;

	const RESET_CONTENT                        = 205;

	const PARTIAL_CONTENT                      = 206;

	const MULTI_STATUS                         = 207;

	const ALREADY_REPORTED                     = 208;

	const IM_USED                              = 226;

	const MULTIPLE_CHOICES                     = 300;

	const MOVED_PERMANENTLY                    = 301;

	const FOUND                                = 302;

	const SEE_OTHER                            = 303;

	const NOT_MODIFIED                         = 304;

	const USE_PROXY                            = 305;

	const SWITCH_PROXY                         = 306;

	const TEMPORARY_REDIRECT                   = 307;

	const PERMANENT_REDIRECT                   = 308;

	const BAD_REQUEST                          = 400;

	const UNAUTHORIZED                         = 401;

	const PAYMENT_REQUIRED                     = 402;

	const FORBIDDEN                            = 403;

	const NOT_FOUND                            = 404;

	const METHOD_NOT_ALLOWED                   = 405;

	const NOT_ACCEPTABLE                       = 406;

	const PROXY_AUTHENTICATION_REQUIRED        = 407;

	const REQUEST_TIMEOUT                      = 408;

	const CONFLICT                             = 409;

	const GONE                                 = 410;

	const LENGTH_REQUIRED                      = 411;

	const PRECONDITION_FAILED                  = 412;

	const REQUEST_ENTITY_TOO_LARGE             = 413;

	const REQUEST_URI_TOO_LONG                 = 414;

	const UNSUPPORTED_MEDIA_TYPE               = 415;

	const REQUESTED_RANGE_NOT_SATISFIABLE      = 416;

	const EXPECTATION_FAILED                   = 417;

	const I_AM_A_TEAPOT                        = 418;

	const AUTHENTICATION_TIMEOUT               = 419;

	const METHOD_FAILURE                       = 420;

	const UNPROCESSABLE_ENTITY                 = 422;

	const LOCKED                               = 423;

	const FAILED_DEPENDENCY                    = 424;

	const UNORDERED_COLLECTION                 = 425;

	const UPGRADE_REQUIRED                     = 426;

	const PRECONDITION_REQUIRED                = 428;

	const TOO_MANY_REQUESTS                    = 429;

	const REQUEST_HEADER_FIELDS_TOO_LARGE      = 431;

	const NO_RESPONSE                          = 444;

	const RETRY_WITH                           = 449;

	const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;

	const UNAVAILABLE_FOR_LEGAL_REASONS        = 451;

	const REQUEST_HEADER_TOO_LARGE             = 494;

	const CERT_ERROR                           = 495;

	const NO_CERT                              = 496;

	const HTTP_TO_HTTPS                        = 497;

	const CLIENT_CLOSED_REQUEST                = 499;

	const INTERNAL_SERVER_ERROR                = 500;

	const NOT_IMPLEMENTED                      = 501;

	const BAD_GATEWAY                          = 502;

	const SERVICE_UNAVAILABLE                  = 503;

	const GATEWAY_TIMEOUT                      = 504;

	const HTTP_VERSION_NOT_SUPPORTED           = 505;

	const VARIANT_ALSO_NEGOTIATES              = 506;

	const INSUFFICIENT_STORAGE                 = 507;

	const LOOP_DETECTED                        = 508;

	const BANDWIDTH_LIMIT_EXCEEDED             = 509;

	const NOT_EXTENDED                         = 510;

	const NETWORK_AUTHENTICATION_REQUIRED      = 511;

	const NETWORK_READ_TIMEOUT_ERROR           = 598;

	const NETWORK_CONNECT_TIMEOUT_ERROR        = 599;
}
