<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesDemandData;
use Fortuneglobe\IceHawk\Interfaces\ServesReadRequestData;
use Fortuneglobe\IceHawk\RequestValidators\GetRequestValidator;

/**
 * Class DomainQuery
 *
 * @package Dreiwolt\Backlog
 */
abstract class DomainQuery implements ServesDemandData
{

	/** @var string */
	protected $domain;

	/** @var ServesReadRequestData */
	protected $request;

	/** @var RequestValidator */
	protected $validator;

	/**
	 * @param string                $domain
	 * @param ServesReadRequestData $request
	 */
	final public function __construct( $domain, ServesReadRequestData $request )
	{
		$this->domain    = $domain;
		$this->request   = $request;
		$this->validator = new GetRequestValidator( $this->request );
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
	 * @param GetRequestValidator $validator
	 */
	abstract protected function validate( GetRequestValidator $validator );

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
	public function isExecutable()
	{
		return true;
	}
}
