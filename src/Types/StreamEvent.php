<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

enum StreamEvent
{
	case CLOSING;

	case CLOSED;
}