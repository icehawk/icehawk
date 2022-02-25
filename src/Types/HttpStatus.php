<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

enum HttpStatus: string
{
	case CODE_100 = 'Continue';

	case CODE_101 = 'Switching Protocols';

	case CODE_102 = 'Processing';

	case CODE_103 = 'Early Hints';

	case CODE_200 = 'OK';

	case CODE_201 = 'Created';

	case CODE_202 = 'Accepted';

	case CODE_203 = 'Non-Authoritative Information';

	case CODE_204 = 'No Content';

	case CODE_205 = 'Reset Content';

	case CODE_206 = 'Partial Content';

	case CODE_207 = 'Multi-Status';

	case CODE_208 = 'Already Reported';

	case CODE_226 = 'IM Used';

	case CODE_300 = 'Multiple Choices';

	case CODE_301 = 'Moved Permanently';

	case CODE_302 = 'Found';

	case CODE_303 = 'See Other';

	case CODE_304 = 'Not Modified';

	case CODE_305 = 'Use Proxy';

	case CODE_307 = 'Temporary Redirect';

	case CODE_308 = 'Permanent Redirect';

	case CODE_400 = 'Bad Request';

	case CODE_401 = 'Unauthorized';

	case CODE_402 = 'Payment Required';

	case CODE_403 = 'Forbidden';

	case CODE_404 = 'Not Found';

	case CODE_405 = 'Method Not Allowed';

	case CODE_406 = 'Not Acceptable';

	case CODE_407 = 'Proxy Authentication Required';

	case CODE_408 = 'Request Timeout';

	case CODE_409 = 'Conflict';

	case CODE_410 = 'Gone';

	case CODE_411 = 'Length Required';

	case CODE_412 = 'Precondition Failed';

	case CODE_413 = 'Payload Too Large';

	case CODE_414 = 'URI Too Long';

	case CODE_415 = 'Unsupported Media Type';

	case CODE_416 = 'Range Not Satisfiable';

	case CODE_417 = 'Expectation Failed';

	case CODE_421 = 'Misdirected Request';

	case CODE_422 = 'Unprocessable Entity';

	case CODE_423 = 'Locked';

	case CODE_424 = 'Failed Dependency';

	case CODE_425 = 'Too Early';

	case CODE_426 = 'Upgrade Required';

	case CODE_428 = 'Precondition Required';

	case CODE_429 = 'Too Many Requests';

	case CODE_431 = 'Request Header Fields Too Large';

	case CODE_451 = 'Unavailable For Legal Reasons';

	case CODE_500 = 'Internal Server Error';

	case CODE_501 = 'Not Implemented';

	case CODE_502 = 'Bad Gateway';

	case CODE_503 = 'Service Unavailable';

	case CODE_504 = 'Gateway Timeout';

	case CODE_505 = 'HTTP Version Not Supported';

	case CODE_506 = 'Variant Also Negotiates';

	case CODE_507 = 'Insufficient Storage';

	case CODE_508 = 'Loop Detected';

	case CODE_510 = 'Not Extended';

	case CODE_511 = 'Network Authentication Required';

	/**
	 * @param int $code
	 *
	 * @return HttpStatus
	 * @throws InvalidArgumentException
	 */
	public static function fromCode( int $code ) : self
	{
		foreach ( self::cases() as $httpStatus )
		{
			if ( $code === $httpStatus->getCode() )
			{
				return $httpStatus;
			}
		}

		throw new InvalidArgumentException( 'Invalid code for HttpStatus: ' . $code );
	}

	public function getCode() : int
	{
		return (int)substr( $this->name, -3 );
	}

	public function getPhrase() : string
	{
		return $this->value;
	}

	#[Pure]
	public function toString() : string
	{
		return sprintf( '%d %s', $this->getCode(), $this->getPhrase() );
	}
}