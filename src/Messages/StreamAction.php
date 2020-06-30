<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use Closure;
use IceHawk\IceHawk\Messages\Interfaces\HandlesStreamAction;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

final class StreamAction implements Interfaces\HandlesStreamAction
{
	private const   ON_CLOSING = 'onClosing';

	private const   ON_CLOSED  = 'onClosed';

	private const   ALL        = [
		self::ON_CLOSING,
		self::ON_CLOSED,
	];

	private string $eventName;

	private Closure $action;

	/**
	 * @param string  $eventName
	 * @param Closure $action
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $eventName, Closure $action )
	{
		$this->guardEventNameIsValid( $eventName );

		$this->eventName = $eventName;
		$this->action    = $action;
	}

	/**
	 * @param string $eventName
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardEventNameIsValid( string $eventName ) : void
	{
		if ( !in_array( $eventName, self::ALL, true ) )
		{
			throw new InvalidArgumentException( 'Invalid event name for stream action: ' . $eventName );
		}
	}

	/**
	 * @param string   $eventName
	 * @param callable $action
	 *
	 * @return HandlesStreamAction
	 * @throws InvalidArgumentException
	 */
	public static function new( string $eventName, callable $action ) : HandlesStreamAction
	{
		return new self( $eventName, Closure::fromCallable( $action ) );
	}

	/**
	 * @param callable $action
	 *
	 * @return HandlesStreamAction
	 * @throws InvalidArgumentException
	 */
	public static function onClosing( callable $action ) : HandlesStreamAction
	{
		return new self( self::ON_CLOSING, Closure::fromCallable( $action ) );
	}

	/**
	 * @param callable $action
	 *
	 * @return HandlesStreamAction
	 * @throws InvalidArgumentException
	 */
	public static function onClosed( callable $action ) : HandlesStreamAction
	{
		return new self( self::ON_CLOSED, Closure::fromCallable( $action ) );
	}

	public function execute( StreamInterface $stream ) : void
	{
		$action = $this->action;

		$action( $stream );
	}

	public function getEventName() : string
	{
		return $this->eventName;
	}
}