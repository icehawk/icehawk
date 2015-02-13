<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestValidators;

use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\Validation\FluidValidator;

/**
 * Class PostRequestValidator
 *
 * @package Fortuneglobe\IceHawk\RequestValidators
 *
 * @method PostRequestValidator uploadedOneFile($var, $message)
 * @method PostRequestValidator uploadedMultipleFiles($var, $message)
 */
final class PostRequestValidator extends FluidValidator
{

	/** @var ServesPostRequestData */
	protected $request;

	/**
	 * @param ServesPostRequestData $request
	 */
	public function __construct( ServesPostRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $var
	 *
	 * @return array|mixed|null|string
	 */
	protected function getValue( $var )
	{
		return $this->request->get( $var );
	}

	/**
	 * @param string $var
	 *
	 * @return bool
	 */
	protected function checkUploadedOneFile( $var )
	{
		$files = $this->request->getFiles( $var );

		if ( count( $files ) == 1 )
		{
			return $files[0]->didUploadSucceed();
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string $var
	 *
	 * @return bool
	 */
	protected function checkUploadedMultipleFiles( $var )
	{
		$files = $this->request->getFiles( $var );

		if ( !empty($files) )
		{
			$boolResult = true;
			foreach ( $files as $fileInfo )
			{
				$boolResult |= $fileInfo->didUploadSucceed();
			}

			return $boolResult;
		}
		else
		{
			return false;
		}
	}
}
