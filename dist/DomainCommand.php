<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesApiData;
use Fortuneglobe\IceHawk\Interfaces\ServesCommandData;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\RequestValidators\PostRequestValidator;

/**
 * Class DomainCommand
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommand implements ServesCommandData
{

	const KEY_SUCCESS_URL = 'success_url';

	const KEY_FAIL_URL    = 'fail_url';

	/** @var ServesApiData */
	protected $api;

	/** @var string */
	protected $domain;

	/** @var PostRequest */
	protected $request;

	/** @var PostRequestValidator */
	protected $validator;

	/** @var Responder */
	protected $responder;

	/**
	 * @param ServesApiData $api
	 * @param string        $domain
	 * @param PostRequest   $request
	 */
	final public function __construct( ServesApiData $api, $domain, PostRequest $request )
	{
		$this->api       = $api;
		$this->domain    = $domain;
		$this->request   = $request;
		$this->validator = $this->getValidator();
		$this->responder = new Responder( $api );
	}

	/**
	 * @return ServesApiData
	 */
	public function getApi()
	{
		return $this->api;
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
	 * @return PostRequestValidator
	 */
	protected function getValidator()
	{
		return new PostRequestValidator( $this->request );
	}

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
	 * @return Responder
	 */
	public function getResponder()
	{
		return $this->responder;
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
