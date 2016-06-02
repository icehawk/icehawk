# TODO

* Allow to configure a base URI of the web app
* Add CSRF form handling
* Add generic accessors to session registry for form objects
* (Add handle method for each interface)?
* Input filtering for Command and Query
* Require a ServesResponse object? from concrete RequestHandler?

```
- Form::new(id)
 |- Id
 |- Token
 |- DefaultData
 |- Data
 `- Feedback
```

## Request validation / Responding

* Request should be validated before building Command/Query
* Command/Query should have typed getters
* RequestHanlder should know about HTTP, Command-/QueryHandler should not
* Command-/QueryHandler should return a result object
* RequestHandler produces a HTTP response out of the result object

## Give opportunity to inject route configurations

### Route

```php
<?php

abstract class Route implements RoutesToHandler
{
    private $requestMethod;
    
    private $uriPattern;
    
    private $requestHandler;
    
    public function __construct(string $requestMethod, string $uriPattern, HandlesRequest $requestHandler)
    {
        $this->requestMethod = $requestMethod;
        $
    }
    
    public
}
```