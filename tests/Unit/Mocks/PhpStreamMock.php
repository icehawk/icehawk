<?php declare(strict_types=1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

/**
 * @source http://news-from-the-basement.blogspot.de/2011/07/mocking-phpinput.html
 */

namespace IceHawk\IceHawk\Tests\Unit\Mocks;

/**
 * Class PhpStreamMock
 * @package IceHawk\IceHawk\Tests\Unit\Mocks
 */
class PhpStreamMock
{
	protected $index  = 0;

	protected $length;

	protected $data   = 'hello world';

	public    $context;

	public function __construct()
	{
		$this->data   = file_exists( $this->buffer_filename() ) ? file_get_contents( $this->buffer_filename() ) : '';
		$this->index  = 0;
		$this->length = strlen( $this->data );
	}

	protected function buffer_filename() : string
	{
		return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_input.txt';
	}

	public function stream_open( $path, $mode, $options, &$opened_path ) : bool
	{
		return true;
	}

	public function stream_close()
	{
	}

	public function stream_stat() : array
	{
		return [];
	}

	public function stream_flush() : bool
	{
		return true;
	}

	/**
	 * @param int $count
	 *
	 * @return bool|string
	 */
	public function stream_read( $count )
	{
		if ( null === $this->length )
		{
			$this->length = strlen( $this->data );
		}

		$length      = min( $count, $this->length - $this->index );
		$data        = substr( $this->data, $this->index );
		$this->index += $length;

		return $data;
	}

	public function stream_eof() : bool
	{
		return ($this->index >= $this->length);
	}

	/**
	 * @param string $data
	 *
	 * @return bool|int
	 */
	public function stream_write( $data )
	{
		return file_put_contents( $this->buffer_filename(), $data );
	}

	public function unlink()
	{
		if ( file_exists( $this->buffer_filename() ) )
		{
			unlink( $this->buffer_filename() );
		}

		$this->data   = '';
		$this->index  = 0;
		$this->length = 0;
	}
}
