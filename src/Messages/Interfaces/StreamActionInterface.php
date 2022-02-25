<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use IceHawk\IceHawk\Types\StreamEvent;
use Psr\Http\Message\StreamInterface;

interface StreamActionInterface
{
	public function execute( StreamInterface $stream ) : void;

	public function getEvent() : StreamEvent;
}