<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Forms;

/**
 * Class FormData
 *
 * @package Fortuneglobe\IceHawk\Forms
 */
class FormData
{

	/** @var array|FormFeedback[] */
	private $feedbacks = [ ];

	/**
	 * @param string $key
	 * @param array  $messages
	 * @param string $severity
	 */
	public function addFeedback( $key, array $messages, $severity = FormFeedback::DANGER )
	{
		$feedback = new FormFeedback( $key, $messages, $severity );

		$this->feedbacks[ $key ] = $feedback;
	}

	/**
	 * @param string $key
	 *
	 * @return array|string[]
	 */
	public function getMessages( $key )
	{
		if ( $this->feedbackExistsForKey( $key ) )
		{
			return $this->getFeedbackForKey( $key )->getMessages();
		}
		else
		{
			return [ ];
		}
	}

	/**
	 * @return array
	 */
	public function getAllMessages()
	{
		$allMessages = [ ];
		foreach ( $this->feedbacks as $feedback )
		{
			$allMessages = array_merge( $allMessages, $feedback->getMessages() );
		}

		return $allMessages;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function feedbackExistsForKey( $key )
	{
		return (isset($this->feedbacks[ $key ]) && ($this->feedbacks[ $key ] instanceof FormFeedback));
	}

	/**
	 * @param string $key
	 *
	 * @return FormFeedback
	 */
	private function getFeedbackForKey( $key )
	{
		return $this->feedbacks[ $key ];
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function getSeverity( $key )
	{
		if ( $this->feedbackExistsForKey( $key ) )
		{
			return $this->getFeedbackForKey( $key )->getSeverity();
		}
		else
		{
			return FormFeedback::NONE;
		}
	}
}