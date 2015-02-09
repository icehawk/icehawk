<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesDemandData;
use Fortuneglobe\IceHawk\Interfaces\ServesWriteRequestData;
use Fortuneglobe\IceHawk\RequestValidators\PostRequestValidator;

/**
 * Class DomainCommand
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommand implements ServesDemandData
{

	const KEY_SUCCESS_URL = 'success_url';

	const KEY_FAIL_URL    = 'fail_url';

	/** @var string */
	protected $domain;

	/** @var ServesWriteRequestData */
	protected $request;

	/**
	 * @param string                 $domain
	 * @param ServesWriteRequestData $request
	 */
	final public function __construct( $domain, ServesWriteRequestData $request )
	{
		$this->domain    = $domain;
		$this->request   = $request;
		$this->validator = new PostRequestValidator( $this->request );
	}

	/**
	 * @return string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		$this->validator->reset();
		$this->validate( $this->validator );

		$this->validator->isNonEmptyStringOrNull( self::KEY_SUCCESS_URL, 'Invalid redirect target for success url.' );
		$this->validator->isNonEmptyStringOrNull( self::KEY_FAIL_URL, 'Invalid redirect target for fail url.' );

		return $this->validator->getBoolResult();
	}

	/**
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->validator->getMessages();
	}

	/**
	 * @param PostRequestValidator $validator
	 */
	abstract protected function validate( PostRequestValidator $validator );

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( $key )
	{
		return $this->request->get( $key );
	}

	/**
	 * @return bool
	 */
	public function hasSuccessUrl()
	{
		return !is_null( $this->getSuccessUrl() );
	}

	/**
	 * @return string
	 */
	public function getSuccessUrl()
	{
		return $this->getRequestValue( self::KEY_SUCCESS_URL );
	}

	/**
	 * @return bool
	 */
	public function hasFailUrl()
	{
		return !is_null( $this->getFailUrl() );
	}

	/**
	 * @return string
	 */
	public function getFailUrl()
	{
		return $this->getRequestValue( self::KEY_FAIL_URL );
	}

	/**
	 * @return bool
	 */
	public function isExecutable()
	{
		return true;
	}
}
