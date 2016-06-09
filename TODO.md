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

## Removed

* Everything related to rewriting (not necessary, because there is no default routing anymore)
* BodyParserFactory requirements
* UriResolvers
* SessionRegistry => Should go into own package

## Request validation / Responding

* Request should be validated before building Command/Query
* Command/Query should have typed getters
* RequestHanlder should know about HTTP, Command-/QueryHandler should not
* Command-/QueryHandler should return a result object
* RequestHandler produces a HTTP response out of the result object

## Body parsing

Having only one possible body parser for all requests with the same content-type is too restrictive.

 * Add an `applyBodyParser(ParsesRequestBody)` to the request interface
 * Call that in the concrete request handler before request validation
 * No need for a factory, less magic, more intentional
 * Move body parsers to an own package?
 * Parser can be used stand-alone or can be applied to the request (and change its data)

## Responding

* Response-Object / -Interface should be injected to the request handler->handle()
* Response-Object should be implemented as a stream for continuous output flushing (see symfony's OutputInterface)
* Create then respose objects that get output object injected (new TwigPage($output)->respond('/path/to/template.twig'))


## Bugs

 * ReadRequestHandler creates a request object only from uri params of the route. Merge with $_GET is missing?