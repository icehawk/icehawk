<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesApiData;
use Fortuneglobe\IceHawk\Interfaces\ServesCommandData;
use Fortuneglobe\IceHawk\Requests\PostRequest;

/**
 * Class DomainCommand
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainCommand implements ServesCommandData
{

	/** @var ServesApiData */
	protected $api;

	/** @var string */
	protected $domain;

	/** @var PostRequest */
	protected $request;

	/** @var PostRequestValidator */
	protected $validator;

	/** @var Responde */
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

		$this->validator->notEmptyStringOrNull( 'redirect_target', 'Invalid redirect target.' );

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
	 * @return bool
	 */
	public function needsLoggedInUser()
	{
		return true;
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
	public function hasRedirectTarget()
	{
		return !is_null( $this->getRedirectTarget() );
	}

	/**
	 * @return string
	 */
	public function getRedirectTarget()
	{
		return $this->getRequestValue( 'redirect_target' );
	}
}
