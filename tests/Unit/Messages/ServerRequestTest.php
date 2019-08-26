<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;
use Generator;
use IceHawk\IceHawk\Exceptions\RuntimeException;
use IceHawk\IceHawk\Messages\ServerRequest;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\Uri;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ServerRequestTest extends TestCase
{
    private const VALID_HEADER_NAME = 'Authorization';

    public function setUp() : void
    {
        $_SERVER['REQUEST_TIME']       = microtime();
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
    }

    public function testItReturnsServerProtocol() :void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'test';

        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals('test', $serverRequest->getProtocolVersion());

        unset($_SERVER['SERVER_PROTOCOL']);

        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals('HTTP/1.1', $serverRequest->getProtocolVersion());
    }

    public function testWithProtocolVersion() : void
    {
        $protocolVersion       = 'HTTP/1.2';
        $serverRequest         = ServerRequest::fromGlobals();
        $clonedRequestInstance = $serverRequest->withProtocolVersion($protocolVersion);

        $this->assertEquals($protocolVersion, $clonedRequestInstance->getProtocolVersion());
    }

    public function testHeaderLine() : void
    {
        $headerValueArray = 'Basic foo, Bearer bar';
        $_SERVER['HTTP_AUTHORIZATION'] = $headerValueArray;
        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals($headerValueArray, $serverRequest->getHeaderLine(self::VALID_HEADER_NAME));
        $this->assertEmpty($serverRequest->getHeaderLine('foo'));
    }

    public function testWithHeader() : void
    {
        $headerValue = 'Basic test';
        $_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

        $serverRequest = ServerRequest::fromGlobals()->withHeader(self::VALID_HEADER_NAME, $headerValue);
        $headers = $serverRequest->getHeaders();
        $this->assertIsArray($headers);
        $this->assertCount(1, $headers);

        $header = $serverRequest->getHeader(self::VALID_HEADER_NAME);
        $this->assertEquals($headerValue, array_shift($header));
    }

    public function testWithAddedHeader() : void
    {
        $expectedCountHeaders = 2;
        $headerValue = 'Basic test';
        $headerValueArray = 'Basic foo, Bearer bar';
        $_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

        $serverRequest = ServerRequest::fromGlobals()->withHeader(self::VALID_HEADER_NAME, $headerValue);
        $anotherClonedInstance = $serverRequest->withAddedHeader(self::VALID_HEADER_NAME, $headerValueArray);

        $this->assertTrue($anotherClonedInstance->hasHeader(self::VALID_HEADER_NAME));
        $this->assertCount($expectedCountHeaders, $anotherClonedInstance->getHeaders()[self::VALID_HEADER_NAME]);

        $withAddedHeaderInstance = $anotherClonedInstance->withAddedHeader('foo', 'bar');
        $this->assertCount($expectedCountHeaders, $withAddedHeaderInstance->getHeaders()[self::VALID_HEADER_NAME]);
        $this->assertFalse($withAddedHeaderInstance->hasHeader('foo'));
    }

    public function testWithoutHeader() : void
    {
        $headerValue = 'Basic test';
        $_SERVER['HTTP_USER_AGENT'] = 'bar';
        $_SERVER['HTTP_AUTHORIZATION'] = $headerValue;

        $serverRequest   = ServerRequest::fromGlobals();
        $clonedInstance  = $serverRequest->withoutHeader('User-Agent');
        $headers         = $clonedInstance->getHeaders();

        $this->assertCount(1, $headers);
        $this->assertFalse($clonedInstance->hasHeader('User-Agent'));

        $anotherInstance = $serverRequest->withoutHeader('foo');
        $headers = $anotherInstance->getHeaders();

        $this->assertIsArray($headers);
        $this->assertCount(2, $headers);

        $header = $serverRequest->getHeader(self::VALID_HEADER_NAME);
        $this->assertEquals($headerValue, array_shift($header));
    }

    public function testItReturnsBody() : void
    {
        $serverRequest = ServerRequest::fromGlobals();
        $stream = new Stream('php://memory', 'w+b');
        $stream->write('Unit-Test');

        $anotherServerRequest = $serverRequest->withBody($stream);
        $this->assertSame($anotherServerRequest->getBody()->__toString(), $stream->__toString());
    }

    public function testWithRequestTarget() : void
    {
        $expectedServerParameters = [
            'REQUEST_URI' => '/api/v1/streams/mega-stream/events',
            'QUERY_STRING' => 'eventName=OrderPlaced&limit=2'
        ];

        $uri = 'https://api.localhost/api/v1/streams/mega-stream/events?eventName=OrderPlaced&limit=2';

        $serverRequest   = ServerRequest::fromGlobals();
        $anotherInstance = $serverRequest->withRequestTarget($uri);

        $this->assertEquals($expectedServerParameters['REQUEST_URI'], $anotherInstance->getServerParams()['REQUEST_URI']);
        $this->assertEquals($expectedServerParameters['QUERY_STRING'], $anotherInstance->getServerParams()['QUERY_STRING']);
    }

    public function testItReturnsRequestMethod() : void
    {
        $serverRequest = ServerRequest::fromGlobals();
        $this->assertEquals('UNKNOWN', $serverRequest->getMethod());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $serverRequest = ServerRequest::fromGlobals();
        $this->assertEquals('GET', $serverRequest->getMethod());
    }

    public function testWithMethod() : void
    {
        $requestMethod   = 'GET';
        $serverRequest   = ServerRequest::fromGlobals()->withMethod($requestMethod);

        $this->assertEquals($requestMethod, $serverRequest->getMethod());
    }

    public function testGetUri() : void
    {
        $serverRequest = ServerRequest::fromGlobals();

        $this->assertInstanceOf(Uri::class, $serverRequest->getUri());
    }

    public function testItReturnsCookieParameters() : void
    {
        $_COOKIE['foo'] = 'bar';

        $serverRequest = ServerRequest::fromGlobals();

        $this->assertNotEmpty($serverRequest->getCookieParams());
        $this->assertEquals('bar', $serverRequest->getCookieParams()['foo']);
    }

    public function testWithCookieParameters() : void
    {
        $cookieParameters = ['unit' => 'test'];
        $serverRequest    = ServerRequest::fromGlobals()->withCookieParams($cookieParameters);

        $this->assertEquals($cookieParameters, $serverRequest->getCookieParams());
    }

    public function testItReturnsQueryParameters() : void
    {
        $_GET['foo'] = 'bar';

        $this->assertEquals(['foo' => 'bar'], ServerRequest::fromGlobals()->getQueryParams());
    }

    public function testWithQueryParameters() : void
    {
        $queryParameters = ['foo' => 'bar'];
        $serverRequest   = ServerRequest::fromGlobals()->withQueryParams($queryParameters);

        $this->assertEquals($queryParameters, $serverRequest->getQueryParams());
    }

    public function testItReturnsUploadedFiles() : void
    {
        $_FILES['foo'] = 'bar';

        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals(['foo' => 'bar'], $serverRequest->getUploadedFiles());
    }

    public function testWithUploadedFiles() : void
    {
        $uploadedFiles = ['foo' => 'bar'];
        $serverRequest = ServerRequest::fromGlobals()->withUploadedFiles($uploadedFiles);

        $this->assertEquals($uploadedFiles, $serverRequest->getUploadedFiles());
    }

    public function testItReturnsParsedBody() : void
    {
        $_POST['foo'] = 'bar';

        $this->assertEquals(['foo' => 'bar'], ServerRequest::fromGlobals()->getParsedBody());
    }

    public function testWithParsedBody() : void
    {
        $parsedBody = ['foo' => 'bar'];
        $serverRequest = ServerRequest::fromGlobals()->withParsedBody($parsedBody);

        $this->assertEquals($parsedBody, $serverRequest->getParsedBody());
    }

    public function testItReturnsAttributes() : void
    {
        $attributes = ServerRequest::fromGlobals()->getAttributes();

        $this->assertIsArray($attributes);
        $this->assertEmpty($attributes);
    }
    
    public function testWithAttribute() : void
    {
        $serverRequest = ServerRequest::fromGlobals()->withAttribute('foo', 'bar');

        $this->assertCount(1, $serverRequest->getAttributes());
        $this->assertEquals('bar', $serverRequest->getAttribute('foo'));

    }

    public function testWithoutAttribute() : void
    {
        $serverRequest = ServerRequest::fromGlobals()->withAttribute('foo', 'bar');

        $this->assertCount(1, $serverRequest->getAttributes());
        $this->assertEquals('bar', $serverRequest->getAttribute('foo'));

        $clonedInstance = $serverRequest->withoutAttribute('test');
        $this->assertCount(1, $clonedInstance->getAttributes());
        $this->assertEquals('bar', $clonedInstance->getAttribute('foo'));

        $anotherClonedInstance = $clonedInstance->withoutAttribute('foo');
        $this->assertEmpty($anotherClonedInstance->getAttributes());
    }

    /**
     * @dataProvider invalidInputStringDataProvider
     *
     * @param mixed $value
     */
    public function testGetInputStringThrowsExceptionIfValueIsNotAString($value) : void
    {
        $_REQUEST['foo'] = $value;
        $serverRequest = ServerRequest::fromGlobals();

        $this->expectException(RuntimeException::class);
        
        $serverRequest->getInputString('foo');
    }

    public function invalidInputStringDataProvider() : Generator
    {
        yield [['foo' => 'bar']];
        yield [null];
        yield [3534543];
        yield [100.];
        yield [new stdClass];
    }

    public function testItReturnsInputString() : void
    {
        $_REQUEST['foo'] = 'bar';
        $serverRequest = ServerRequest::fromGlobals();
        
        $this->assertEquals('bar', $serverRequest->getInputString('foo'));
    }

    public function testItReturnsInputStringIfDefaultParameterProvided() : void
    {
        $defaultValue  = 'test';
        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals($defaultValue, $serverRequest->getInputString('unit', $defaultValue));
    }

    /**
     * @dataProvider invalidInputArrayDataProvider
     *
     * @param mixed $value
     */
    public function testGetInputArrayThrowsExceptionIfValueIsNotAString($value) : void
    {
        $_REQUEST['foo'] = $value;
        $serverRequest = ServerRequest::fromGlobals();

        $this->expectException(RuntimeException::class);

        $serverRequest->getInputArray('foo');
    }

    public function invalidInputArrayDataProvider() : Generator
    {
        yield ['test'];
        yield [''];
        yield [null];
        yield [3534543];
        yield [100.];
        yield [new stdClass];
    }

    public function testItReturnsInputArray() : void
    {
        $value = ['unit' => 'test'];
        $_REQUEST['foo'] = $value;
        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals($value, $serverRequest->getInputArray('foo'));
    }

    public function testItReturnsInputArrayIfDefaultParameterProvided() : void
    {
        $defaultValue  = ['unit' => 'test'];
        $serverRequest = ServerRequest::fromGlobals();

        $this->assertEquals($defaultValue, $serverRequest->getInputArray('unit', $defaultValue));
    }
}
