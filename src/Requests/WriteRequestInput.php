<?php
namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;
use Fortuneglobe\IceHawk\Interfaces\ProvidesUploadedFileData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestInputData;
use Fortuneglobe\IceHawk\Mappers\UploadedFilesMapper;

/**
 * Class RequestInput
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
class WriteRequestInput extends AbstractRequestInput implements ProvidesWriteRequestInputData
{
	/**
	 * @var ParsesRequestBody
	 */
	private $requestBodyParser;

	/**
	 * @var array
	 */
	private $uploadedFiles;

	/**
	 * @var string
	 */
	private $body;

	public function __construct( array $uriParams, ParsesRequestBody $requestBodyParser )
	{
		parent::__construct( $uriParams );
		
		$this->requestBodyParser = $requestBodyParser;
	}

	public function getBody() : string
	{
		if ( is_null( $this->body ) )
		{
			$this->body = file_get_contents( 'php://input' );

			$this->body = $this->body ? : '';
		}

		return $this->body;
	}

	public function getAllFiles() : array
	{
		if ( is_null( $this->uploadedFiles ) )
		{
			$this->uploadedFiles = ( new UploadedFilesMapper( $_FILES ) )->mapToInfoObjects();
		}

		return $this->uploadedFiles;
	}
	/**
	 * @param string $fieldKey
	 *
	 * @return array|ProvidesUploadedFileData[]
	 */
	public function getFiles( string $fieldKey ) : array
	{
		return $this->getAllFiles()[ $fieldKey ] ?? [ ];
	}

	/**
	 * @param string $fieldKey
	 * @param int    $fileIndex
	 *
	 * @return ProvidesUploadedFileData|null
	 */
	public function getOneFile( string $fieldKey, int $fileIndex = 0 )
	{
		$files = $this->getFiles( $fieldKey );

		return $files[ $fileIndex ] ?? null;
	}

	protected function getMergedRequestData() : array
	{
		$requestDataFromBody = $this->requestBodyParser->parse( $this->getBody() );
		
		return array_merge( $_POST, $requestDataFromBody, $this->uriParams );
	}
}