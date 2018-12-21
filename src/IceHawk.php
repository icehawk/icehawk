<?php declare(strict_types=1);

namespace IceHawk\IceHawk;

final class IceHawk
{
	public static function withDependencies() : self
	{
		return new self();
	}
}