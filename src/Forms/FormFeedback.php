<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Forms;

/**
 * Class FormFeedback
 *
 * @package Fortuneglobe\IceHawk\Forms
 */
class FormFeedback
{

	const NONE    = '';

	const INFO    = 'info';

	const WARNING = 'warning';

	const DANGER  = 'danger';

	const SUCCESS = 'success';

	/** @var string */
	private $key;

	/** @var array */
	private $messages;

	/** @var string */
	private $severity;

	/**
	 * @param string $key
	 * @param array  $messages
	 * @param string $severity
	 */
	public function __construct( $key, array $messages, $severity = self::DANGER )
	{
		$this->key      = $key;
		$this->messages = $messages;
		$this->severity = $severity;
	}

	/**
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * @return string
	 */
	public function getSeverity()
	{
		return $this->severity;
	}
}