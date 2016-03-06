# CHANGELOG

## Changes in Version 2.0.0

**CAUTION** This release has major interface changes and is not backward compatible with prior versions.

### Added / Moved / Renamed

* Requires php >= 7.0.0
* Added scalar type hints and return types to all interfaces
* Renamed/moved pub/sub related classes and interfaces
  * EventListener => PubSub\AbstractEventSubscriber
  * Interfaces\ListensToEvents => PubSub\Interfaces\SubscribesToEvents
  * Interfaces\ServesEventData => PubSub\Interfaces\CarriesEventData
* Added exceptions in pub/sub context:
  * PubSubException
  * EventSubscriberMethodNotCallable
* Added interfaces for event publisher
* Added EventPublisher as a singleton class
* Added UncaughtExceptionWasThrownEvent that is emitted instead of calling handleUncaughtException on the delegate
* Added RedirectingEvent that is emitted before a redirecting to another URI
* Renamed ServceIceHawkContig interface to ConfiguresIceHawk
* Renamed ControlsHandlingBehaviour interface to SetsUpEnvironment
* Moved default IceHawkConfig to Defaults/IceHawkConfig
* Moved default IceHawkDelegate to Defaults/IceHawkDelegate
* Added Defaults/IceHawkEventSubscriber

### Removed

* Removed Interfaces/RendersTemplate
* Removed Responses/TemplatePage
* Removed handleUncaughtExcpetion from delegate interface

## Changes in Version 1.4.1

 * Fixed regex pattern replacement in UriRewriter class

## Changes in Version 1.4.0

 * Fixed issue #1, added "ext-fileinfo": "*" to composer's require block and a hint to the README.  
 * Closed issue #2, added `setUpEnvironment()` to interface `ControlsHandlingBehaviour` and default class `IceHawkDelegate`.  
 Order of IceHawk initialization is now:
    1. setUpErrorHandling()
    2. setUpSessionHandling()
    3. setUpEnvironment()
 * Added `getRequestInfo()` to interface `ServesEventData`
 * `IceHawkWasInitializedEvent` now provides the `RequestInfo` instance, too.
 * Added internal wrapper for the IceHawk config to make sure all of its provided instances were initialized only once 
 * Refactored internal validation of the IceHawk config
 * Added `getRequestInfo()` to interface `ServesRequestData`
 * `GetRequest` and `PostRequest`, as well as the `DomainQuery` and `DomainCommand` objects now provide the `RequestInfo` instance, too.
 * Refactored `HandlingRequestEvent` and `RequestWasHandledEvent`, only request object is injected
 * Updated tool script `build/tools/update_tools.sh`
 * Removed tool-phars from `build/tools`
 * Updated travis config

## Changes in Version 1.3.1

 * Fixed filename of class `EventListener`

## Changes in Version 1.3.0

 * Restructured the project directories to fit best practice.
 
 * Added PHP QA tools
 * Added `getRawData()` method to `PostRequest`, serving the raw POST data (`php://input`).
 * Added the follwing protected methods to `DomainCommand` to give access to all POST request data:
   * `getRequestData()` serves the whole POST request data array
   * `getRequestRawData()` serves the raw post data (`php://input`)
   * `getAllUploadedFiles()` serves all uploaded files as an assoc. array wrapped in `UploadedFile` objects.
   * `getUploadedFiles($key)` serves all uploaded files for a certain key as num. array wrapped in `UploadedFile` objects.
   * `getOneUploadedFile($key, $fileIndex = 0)` serves one uploaded file for a certain key and num. index wrapped in an `UploadedFile` object. Or `NULL` if there is no file at `$key` and/or `$fileIndex`.
 * Added the following protected methods to `DomainQuery` to give access to all GET request data:
   * `getRequestData()` serves the whole GET request data array
 * Added `InternalServerError` response class and HTTP code in `Http` class.
 * Added the following methods to `RequestInfo` to access the basic auth data:
   * `getAuthUser()`
   * `getAuthPassword()`
   
 * Declared `SessionRegistry` as abstract.
 
 * Removed the following methods from `DomainCommand` to avoid hard coded POST parameter names:
   * `hasSuccessUrl()`
   * `getSuccessUrl()`
   * `hasFailUrl()`
   * `getFailUrl()`
 * Removed `exit()` from `Unauthorized` response.
 * Removed hard coded default setup for error handling and session in `IceHawkDelegate`. Now these settings are based on the system's php defaults.
 * Removed internal class `RequestHandler` because it is obsolete.
 
 * Method `IceHawk->init()` now checks the values served by the injected config object an can throw the following exceptions:
   * `Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation`
   * `Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation`
   * `Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation`
   * `Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace`
   * `Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection`
   
 * Renamed method `getProjectNamespace` to `getDomainNamespace` in `ServesIceHawkConfig` and `IceHawkConfig`.
 * Renamed interface `HandlesIceHawkTasks` to `ControlsHandlingBehaviour`
 * Renamed interface `ListensToIceHawkEvents` to `ListensToEvents`
 * Renamed interface `ServesIceHawkEventData` to `ServesEventData`
 * Renamed interface `WrapsDataOfUploadedFile` to `ServesUploadedFileData`
 * Renamed class `UploadedFileInfo` to `UploadedFile`
 
 * Completed unit tests.
