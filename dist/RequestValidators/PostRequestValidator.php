<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestValidators;

use Fortuneglobe\IceHawk\Interfaces\ServesWriteRequestData;
use Fortuneglobe\IceHawk\RequestValidator;

/**
 * Class PostRequestValidator
 *
 * @package Fortuneglobe\IceHawk\RequestValidators
 *
 * @method PostRequestValidator uploadedOneFile($var, $message)
 * @method PostRequestValidator uploadedMultipleFiles($var, $message)
 */
final class PostRequestValidator extends RequestValidator
{

	/** @var ServesWriteRequestData */
	protected $request;

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
			$bool_result = true;
			foreach ( $files as $file_info )
			{
				$bool_result |= $file_info->didUploadSucceed();
			}

			return $bool_result;
		}
		else
		{
			return false;
		}
	}
}
