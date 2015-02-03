<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesApiData;
use Fortuneglobe\IceHawk\Interfaces\ServesCommandData;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\RequestValidators\GetRequestValidator;

/**
 * Class DomainQuery
 *
 * @package Dreiwolt\Backlog
 */
abstract class DomainQuery implements ServesCommandData
{

	/** @var ServesApiData */
	protected $api;

	/** @var string */
	protected $domain;

	/** @var GetRequest */
	protected $request;

	/** @var RequestValidator */
	protected $validator;

	/** @var Responder */
	protected $responder;

	/**
	 * @param ServesApiData $api
	 * @param string        $domain
	 * @param GetRequest    $request
	 */
	final public function __construct( ServesApiData $api, $domain, GetRequest $request )
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
	 * @param RequestValidator $validator
	 */
	abstract protected function validate( RequestValidator $validator );

	/**
	 * @return RequestValidator
	 */
	protected function getValidator()
	{
		return new GetRequestValidator( $this->request );
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
	public function isExecutable()
	{
		return true;
	}
}
