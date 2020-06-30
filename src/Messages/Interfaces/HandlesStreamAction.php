<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use Psr\Http\Message\StreamInterface;

interface HandlesStreamAction
{
	public function execute( StreamInterface $stream ) : void;

	public function getEventName() : string;
}