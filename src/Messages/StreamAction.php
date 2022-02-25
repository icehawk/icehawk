<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use Closure;
use IceHawk\IceHawk\Messages\Interfaces\StreamActionInterface;
use IceHawk\IceHawk\Types\StreamEvent;
use Psr\Http\Message\StreamInterface;

final class StreamAction implements StreamActionInterface
{
	public static function new( StreamEvent $event, callable $action ) : StreamActionInterface
	{
		return new self( $event, $action(...) );
	}

	private function __construct( private StreamEvent $event, private Closure $action ) { }

	public static function onClosing( callable $action ) : StreamActionInterface
	{
		return self::new( StreamEvent::CLOSING, $action(...) );
	}

	public static function onClosed( callable $action ) : StreamActionInterface
	{
		return self::new( StreamEvent::CLOSED, $action(...) );
	}

	public function execute( StreamInterface $stream ) : void
	{
		$action = $this->action;

		$action( $stream );
	}

	public function getEvent() : StreamEvent
	{
		return $this->event;
	}
}