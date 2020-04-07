<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\UploadedFile;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use function sys_get_temp_dir;
use function tempnam;
use const UPLOAD_ERR_OK;

final class UploadedFileTest extends TestCase
{
	private UploadedFileInterface $uploadedFile;

	private string $tempName;

	private array $files;

	public function setUp() : void
	{
		$this->tempName = (string)tempnam( sys_get_temp_dir(), 'success' );
		$primitiveArray = [
			'name'     => 'test.txt',
			'type'     => 'text/plain',
			'tmp_name' => $this->tempName,
			'error'    => UPLOAD_ERR_OK,
			'size'     => 563,
		];

		$this->uploadedFile = UploadedFile::fromArray( $primitiveArray );
		$this->files        = [];
	}

	public function tearDown() : void
	{
		foreach ( $this->files as $file )
		{
			if ( is_scalar( $file ) && file_exists( $file ) )
			{
				unlink( $file );
			}
		}
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testItCanBeCreatedFromPrimitiveArray() : void
	{
		$this->assertEquals( 'text/plain', $this->uploadedFile->getClientMediaType() );
		$this->assertEquals( 'test.txt', $this->uploadedFile->getClientFilename() );
		$this->assertEquals( 563, $this->uploadedFile->getSize() );
		$this->assertEquals( UPLOAD_ERR_OK, $this->uploadedFile->getError() );
		$this->assertEquals( $this->tempName, $this->uploadedFile->getStream()->getMetadata( 'uri' ) );
	}

	/**
	 * @dataProvider invalidPathsDataProvider
	 *
	 * @param mixed $path
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testMoveToRaisesExceptionForInvalidPath( $path ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Target path is empty' );

		$this->uploadedFile->moveTo( $path );
	}

	public function invalidPathsDataProvider() : array
	{
		return [
			'empty' => [''],
			'null'  => [null],
			'int'   => [143],
			'true'  => [true],
			'false' => [false],
			'float' => [14.14],
			'array' => [['test']],
		];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testSuccessFullMoveTo() : void
	{
		$stream = new Stream( $this->tempName, 'w+b' );
		$stream->write( 'foo bar.' );

		$to            = $this->tempName;
		$this->files[] = $to;

		$this->uploadedFile->moveTo( $to );
		$this->assertFileExists( $to );
		$this->assertEquals( (string)$stream, file_get_contents( $to ) );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testMoveToThrowsExceptionIfMoveWasNotSuccessful() : void
	{
		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Could not move uploaded file' );

		$this->uploadedFile->moveTo( '/unknown/bar' );
	}
}
