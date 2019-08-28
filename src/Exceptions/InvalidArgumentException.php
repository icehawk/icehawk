<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function invalidFileMovePath() : self
    {
        return new self('Invalid path provided for move operation. Must be a non-empty string.');
    }
}
