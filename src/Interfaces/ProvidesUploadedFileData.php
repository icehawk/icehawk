<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Class UploadedFile
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesUploadedFileData
{
	/**
	 * @return int
	 */
	public function getError() : int;

	/**
	 * @return string
	 */
	public function getName() : string;

	/**
	 * @return int
	 */
	public function getSize() : int;

	/**
	 * @return string
	 */
	public function getTmpName() : string;

	/**
	 * @return string
	 */
	public function getType() : string;

	/**
	 * @return string
	 */
	public function getRealType() : string;

	/**
	 * @return string
	 */
	public function getEncoding() : string;

	/**
	 * @return bool
	 */
	public function didUploadSucceed() : bool;

	/**
	 * @return string
	 */
	public function getErrorMessage() : string;
}
