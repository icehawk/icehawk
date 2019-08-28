<?php declare(strict_types=1);

namespace Fortuneglobe\IceLynx\Server\Tests\Unit;

use IceHawk\IceHawk\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

final class UploadedFileTest extends TestCase
{
    /** @var UploadedFileInterface  */
    private $uploadedFile;
    /** @var bool|string */
    private $tempName;

    private $files;

    public function setUp() : void
    {
        $this->tempName = tempnam(sys_get_temp_dir(), 'success');
        $primitiveArray = [
            'name'      => 'test.txt',
            'type'      => 'text/plain',
            'tmp_name'  => $this->tempName,
            'error'     => UPLOAD_ERR_OK,
            'size'      => 563
        ];

        $this->uploadedFile = UploadedFile::fromArray($primitiveArray);
        $this->files = [];
    }

    public function tearDown() : void
    {
        foreach ($this->files as $file) {
            if (is_scalar($file) && file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testItCanBeCreatedFromPrimitiveArray() : void
    {
        $this->assertEquals('text/plain', $this->uploadedFile->getClientMediaType());
        $this->assertEquals('test.txt', $this->uploadedFile->getClientFilename());
        $this->assertEquals(563, $this->uploadedFile->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $this->uploadedFile->getError());
        $this->assertEquals($this->tempName, $this->uploadedFile->getStream()->getMetadata('uri'));
    }

    /**
     * @dataProvider invalidPathsDataProvider
     */
    public function testMoveToRaisesExceptionForInvalidPath($path) : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->uploadedFile->moveTo($path);
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

    public function testSuccessFullMoveTo() : void
    {
        $stream = new Stream($this->tempName, 'w+b');
        $stream->write('foo bar.');

        $this->files[] = $to = $this->tempName;

        $this->uploadedFile->moveTo($to);
        $this->assertFileExists($to);
        $this->assertEquals((string)$stream, file_get_contents($to));
    }
}
