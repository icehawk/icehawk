# Request handling

This is the flow of handling a request:

```
- App handler (a QueuedRequestHandler)
  |- App middleware 1
  |- App middleware 2
  |- App middleware n
  `- Route handler (a QueuedRequestHandler as fallback handler of App handler)
     |- Middleware 1 for matching route
     |- Middleware 2 for matching route
     |- Middleware n for matching route
     `- Fallback handler (a fallback handler of Route handler) 
```

The Route handler only becomes active, if none of the App middleware returned a response.

The Fallback handler only becomes active, if none of the middlewares for the matching route returned a response.

The latter throws a `RequestHandlingFailedException`, if no matching route was found.

The thrown exception states:

* Message: "No responder found."
* Code: 404
* Previous: `new LogicException('No responder found.', 404)`

The original request can be received from `RequestHandlingFailedException#getRequest()`.

This allows to add an App middleware that catches the `RequestHandlingFailedException` and produces a custom error page.
Such an App middleware could look like this:

```php
<?php declare(strict_types=1);

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;

final class ErrorPageMiddleware implements MiddlewareInterface
{
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ) : ResponseInterface
    {
        try 
        {
            return $handler->handle( $request );
        }
        catch ( RequestHandlingFailedException $e )
        {
            return Response::newWithContent( 'Custom 404 page content here.' )->withStatus( $e->getCode() );
        }
    }
}
```