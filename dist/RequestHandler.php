<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\DomainRequestHandlers\CommandHandler;
use Fortuneglobe\IceHawk\DomainRequestHandlers\QueryHandler;
use Fortuneglobe\IceHawk\Exceptions\IceHawkException;
use Fortuneglobe\IceHawk\Exceptions\InvalidApiCalled;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestMethod;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestHandlerConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUploadedFiles;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Responses\BadRequest;
use Fortuneglobe\IceHawk\Responses\NotFound;

/**
 * Class RequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestHandler
{

	/** @var ServesRequestInfo */
	private $request_info;

	/** @var ServesRequestHandlerConfig */
	private $config_delegate;

	/** @var array */
	private $post_data = [ ];

	/** @var array */
	private $get_data = [ ];

	/** @var array */
	private $uploaded_files = [ ];

	/** @var ServesUriComponents */
	private $uri_components;

	/** @var array */
	private static $valid_methods = [ Http::METHOD_GET, Http::METHOD_POST ];

	/**
	 * @param ServesRequestInfo          $request_info
	 * @param ServesRequestHandlerConfig $config_delegate
	 * @param array                      $get_data
	 * @param array                      $post_data
	 * @param array                      $uploaded_files
	 *
	 * @throws InvalidApiCalled
	 * @throws InvalidRequestMethod
	 * @throws MalformedRequestUri
	 */
	public function __construct(
		ServesRequestInfo $request_info,
		ServesRequestHandlerConfig $config_delegate,
		array $get_data,
		array $post_data,
		array $uploaded_files
	)
	{
		$this->request_info    = $request_info;
		$this->config_delegate = $config_delegate;
		$this->get_data        = $get_data;
		$this->post_data       = $post_data;
		$this->uploaded_files  = $uploaded_files;
	}

	public function handle()
	{
		try
		{
			$this->guardValidRequestMethod();

			$this->redirectIfNeeded();

			$this->uri_components = $this->getUriComponents();

			$this->guardValidApi();

			$request                = $this->getRequest();
			$domain_request_handler = $this->getDomainRequestHandler();

			$domain_request_handler->handleRequest( $request );
		}
		catch ( InvalidRequestMethod $e )
		{
			( new BadRequest( [ 'Invalid request method.' ] ) )->respond();
		}
		catch ( InvalidApiCalled $e )
		{
			( new NotFound() )->respond();
		}
		catch ( MalformedRequestUri $e )
		{
			( new NotFound() )->respond();
		}
		catch ( IceHawkException $e )
		{
			( new BadRequest( [ $e->getMessage() ] ) )->respond();
		}
	}

	/**
	 * @throws InvalidRequestMethod
	 */
	private function guardValidRequestMethod()
	{
		$request_method = $this->getRequestMethodUpperCase();

		if ( !in_array( $request_method, self::$valid_methods ) )
		{
			throw new InvalidRequestMethod( $request_method );
		}
	}

	/**
	 * @return string
	 */
	private function getRequestMethodUpperCase()
	{
		return strtoupper( $this->request_info->getMethod() );
	}

	private function redirectIfNeeded()
	{
		$uri_rewriter = $this->config_delegate->getUriRewriter();
		$redirect     = $uri_rewriter->rewrite( $this->request_info );

		if ( !$redirect->urlEquals( $this->request_info->getUri() ) )
		{
			$redirect->respond();
		}
	}

	/**
	 * @return ServesUriComponents
	 */
	private function getUriComponents()
	{
		$uri_resolver = $this->config_delegate->getUriResolver();

		return $uri_resolver->resolveUri( $this->request_info );
	}

	/**
	 * @throws InvalidApiCalled
	 */
	private function guardValidApi()
	{
		$api_name = $this->uri_components->getApiName();
		if ( !in_array( $api_name, Api::getAll() ) )
		{
			throw new InvalidApiCalled( $api_name );
		}
	}

	/**
	 * @return GetRequest|PostRequest|ServesRequestData|ServesUploadedFiles
	 */
	private function getRequest()
	{
		$request_method = $this->getRequestMethodUpperCase();

		if ( $request_method == Http::METHOD_POST )
		{
			return new PostRequest( $this->post_data, $this->uploaded_files );
		}
		else
		{
			return new GetRequest( $this->get_data );
		}
	}

	/**
	 * @throws InvalidApiCalled
	 * @return CommandHandler|QueryHandler
	 */
	private function getDomainRequestHandler()
	{
		$request_method = $this->getRequestMethodUpperCase();

		if ( $request_method == Http::METHOD_POST )
		{
			return new CommandHandler(
				$this->getApi(),
				$this->uri_components->getDomain(),
				$this->uri_components->getDemand(),
				$this->config_delegate->getProjectNamespace()
			);
		}
		else
		{
			return new QueryHandler(
				$this->getApi(),
				$this->uri_components->getDomain(),
				$this->uri_components->getDemand(),
				$this->config_delegate->getProjectNamespace()
			);
		}
	}

	/**
	 * @throws InvalidApiCalled
	 * @return Apis\Json|Apis\Web|Interfaces\ServesApiData
	 */
	private function getApi()
	{
		return Api::factory(
			$this->uri_components->getApiName(),
			$this->uri_components->getApiVersion()
		);
	}
}
