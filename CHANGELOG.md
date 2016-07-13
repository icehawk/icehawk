# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com).

## [2.0.0-rc4] - 2016-07-13

**CAUTION** This release has major interface changes and is not backward compatible with prior versions.

### Added

- Added scalar type hints and return types to all interfaces where possible
- Added exceptions in pub/sub context:
	- `PubSubException`
	- `EventSubscriberMethodNotCallable`
- Added interfaces for event publisher
- Added `EventPublisher`
- Added `Defaults/IceHawkEventSubscriber`
- Completed constants in `HttpMethod` and `HttpCode` with all available values
- Added `Responses/AbstractHttpResponse` with optional additional headers and an abstract get Body
- Added routing by defining route interfaces and adding routers for write, read and options requests
- Added new Event `InitializingIceHawkEvent` that will be published after setting up global vars, but before setting up every other thing

### Changed

- Requires php >= 7.0.0
- Changed vendor namespace to IceHawk
- Renamed/moved pub/sub related classes and interfaces
	- `EventListener` => `PubSub\AbstractEventSubscriber`
	- `Interfaces\ListensToEvents` => `PubSub\Interfaces\SubscribesToEvents`
	- `Interfaces\ServesEventData` => `PubSub\Interfaces\CarriesEventData`
- Renamed `ServeIceHawkConfig` interface to `ConfiguresIceHawk`
- Renamed `ControlsHandlingBehaviour` interface to `SetsUpEnvironment`
- Moved default `IceHawkConfig` to `Defaults/IceHawkConfig`
- Moved default `IceHawkDelegate` to `Defaults/IceHawkDelegate`
- Split abstract class `Http` into `HttpMethod` and `HttpCode`

### Removed

- Removed `Interfaces/RendersTemplate`
- Removed `Responses/TemplatePage`
- Removed `handleUncaughtExcpetion` from delegate interface
- Removed all predefined responses
- Removed `UriResolver` and `UriRewriter`
- Removed `SessionRegistry` ( now in a separate package)

## [1.4.2] - 2016-01-10

- Final 1.* release before moving to https://github.com/icehawk/icehawk.git and version 2.0

## [1.4.1] - 2016-01-10

### Fixed

- Fixed regex pattern replacement in UriRewriter class

## [1.4.0] - 2015-11-27

### Added

- Added `getRequestInfo()` to interface `ServesEventData`
- `IceHawkWasInitializedEvent` now provides the `RequestInfo` instance, too.
- Added internal wrapper for the IceHawk config to make sure all of its provided instances were initialized only once 
- Added `getRequestInfo()` to interface `ServesRequestData`
- `GetRequest` and `PostRequest`, as well as the `DomainQuery` and `DomainCommand` objects now provide the `RequestInfo` instance, too.

### Changed

- Updated tool script `build/tools/update_tools.sh`
- Updated travis config
- Refactored internal validation of the IceHawk config
- Refactored `HandlingRequestEvent` and `RequestWasHandledEvent`, only request object is injected

### Fixed

- Fixed issue #1, added "ext-fileinfo": "*" to composer's require block and a hint to the README.
- Closed issue #2, added `setUpEnvironment()` to interface `ControlsHandlingBehaviour` and default class `IceHawkDelegate`.
 Order of IceHawk initialization is now:
	1. setUpErrorHandling()
    2. setUpSessionHandling()
    3. setUpEnvironment()
    
### Removed

- Removed tool-phars from `build/tools`

## [1.3.1] - 2015-10-02

### Fixed

- Fixed filename of class `EventListener`

## [1.3.0] - 2015-09-23

### Added

- Added PHP QA tools
- Added `getRawData()` method to `PostRequest`, serving the raw POST data (`php://input`).
- Added the follwing protected methods to `DomainCommand` to give access to all POST request data:
	- `getRequestData()` serves the whole POST request data array
    - `getRequestRawData()` serves the raw post data (`php://input`)
    - `getAllUploadedFiles()` serves all uploaded files as an assoc. array wrapped in `UploadedFile` objects.
    - `getUploadedFiles($key)` serves all uploaded files for a certain key as num. array wrapped in `UploadedFile` objects.
    - `getOneUploadedFile($key, $fileIndex = 0)` serves one uploaded file for a certain key and num. index wrapped in an `UploadedFile` object. Or `NULL` if there is no file at `$key` and/or `$fileIndex`.
- Added the following protected methods to `DomainQuery` to give access to all GET request data:
    -`getRequestData()` serves the whole GET request data array
- Added `InternalServerError` response class and HTTP code in `Http` class.
- Added the following methods to `RequestInfo` to access the basic auth data:
    - `getAuthUser()`
    - `getAuthPassword()`
- Completed unit tests.
   
### Changed

- Restructured the project directories to fit best practice.
- Declared `SessionRegistry` as abstract.
- Method `IceHawk->init()` now checks the values served by the injected config object an can throw the following exceptions:
    - `Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation`
    - `Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation`
    - `Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation`
    - `Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace`
    - `Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection`
   
- Renamed method `getProjectNamespace` to `getDomainNamespace` in `ServesIceHawkConfig` and `IceHawkConfig`.
- Renamed interface `HandlesIceHawkTasks` to `ControlsHandlingBehaviour`
- Renamed interface `ListensToIceHawkEvents` to `ListensToEvents`
- Renamed interface `ServesIceHawkEventData` to `ServesEventData`
- Renamed interface `WrapsDataOfUploadedFile` to `ServesUploadedFileData`
- Renamed class `UploadedFileInfo` to `UploadedFile`

### Removed

- Removed the following methods from `DomainCommand` to avoid hard coded POST parameter names:
    - `hasSuccessUrl()`
    - `getSuccessUrl()`
    - `hasFailUrl()`
    - `getFailUrl()`
- Removed `exit()` from `Unauthorized` response.
- Removed hard coded default setup for error handling and session in `IceHawkDelegate`. Now these settings are based on the system's php defaults.
- Removed internal class `RequestHandler` because it is obsolete.

## 1.0.0 - 2015-03-30

- First release

[2.0.0-rc4]: https://github.com/icehawk/icehawk/compare/v1.4.2...v2.0.0-rc4
[1.4.2]: https://github.com/icehawk/icehawk/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/icehawk/icehawk/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/icehawk/icehawk/compare/v1.3.1...v1.4.0
[1.3.1]: https://github.com/icehawk/icehawk/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/icehawk/icehawk/compare/v1.0.0...v1.3.0