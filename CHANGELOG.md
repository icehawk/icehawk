# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com).

## [3.0.0] - YYYY-MM-DD

## [2.2.0] - 2017-09-01

### Fixed

- Code inspection issues and non-optimal array usage in loops
- Non-binary-safe reading of `php://input`, related to ([#31])

### Added

- Method `getBodyAsStream() : resource` to `WriteRequestInput` class. ([#31])

## [2.1.1] - 2017-02-10

### Fixed

- Wrong request info instance used for route matching, when request bypassing is used. ([#27]) 

### Added

- Integration test suite

### Changed

- Test suites updated for PHPUnit 6. ([#28])
- PHPUnit dependency to 6.0.6 (PHAR).

## [2.1.0] - 2016-12-19

### Added

- Method `getCustomValue(string $key) : string` to access custom values via the `RequestInfo` object. ([#15])
- Missing getters for `$_SERVER` indices in `RequestInfo` class and interface `ProvidesRequestInfo`. ([#17])
- Configurable wrapper for cookies with default implementation, accessible via `$request` and all events. ([#26]) 
- Optional request bypassing to reroute requests to another request method without redirect ([#16])
  - `getRequestBypasses()` method added to `ConfiguresIceHawk`
  - `DefaultRequestBypassing` trait added to default IceHawkConfig
  - `RequestBypass` class to configure a request bypass

### Removed

- Class `AbstractHttpResponse` ([#24])

### Changed

- Declared `Options` response class as final ([#24])
- Return value of `WriteRequestInput::getOneFile()` is no longer `NULL`, if the `$fieldKey` or the `$fileIndex` does not exist, 
instead it now returns an empty `UploadedFile` object with the error code `UPLOAD_ERR_NO_FILE` set. ([#22])
- Return type declaration of `WriteRequestInput::getOneFile()` is now `ProvidesUploadedFileData`. ([#22])

## [2.0.4] - 2016-11-12

### Fixed

- Removed obsolete interface inheritance from `HandlesGetRequest` interface ([#23])

## [2.0.3] - 2016-11-04

### Fixed

- Fixed bug that prevents access to uploaded files having a string index ([#20])

## [2.0.2] - 2016-11-03

### Fixed

- Fixed bug that causes a warning when using generators or traversable implementations for routes ([#18])

## [2.0.1] - 2016-10-16

### Fixed

- Check for `HTTPS = On` is now case insensitive (`RequestInfo::isSecure()`) ([#13])

## [2.0.0] - 2016-10-06

### Added

- Matches from a route group are added to the request input data ([#8])
- Sub routes of a route group are guarded to be of a valid type at construction, not at runtime ([#9])

### Changed

- [Contribution guide](./.github/CONTRIBUTING.md)

## [2.0.0-rc5] - 2016-09-27

### Added

- `ReadRequestInput`/`WriteRequestInput`'s get method now support an optional default value, in case it was asked for a non-existing key. ([#1])

### Changed

- Renamed accessors for RequestInfo and InputData on `$request` object to `$request->getInfo()` and `$request->getInput()`
- All php files are setting strict types `declare(strict_types = 1);` ([#2])

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

[3.0.0]: https://github.com/icehawk/icehawk/compare/v2.2.0...v3.0.0
[2.2.0]: https://github.com/icehawk/icehawk/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/icehawk/icehawk/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/icehawk/icehawk/compare/v2.0.4...v2.1.0
[2.0.4]: https://github.com/icehawk/icehawk/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/icehawk/icehawk/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/icehawk/icehawk/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/icehawk/icehawk/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/icehawk/icehawk/compare/v2.0.0-rc5...v2.0.0
[2.0.0-rc5]: https://github.com/icehawk/icehawk/compare/v2.0.0-rc4...v2.0.0-rc5
[2.0.0-rc4]: https://github.com/icehawk/icehawk/compare/v1.4.2...v2.0.0-rc4
[1.4.2]: https://github.com/icehawk/icehawk/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/icehawk/icehawk/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/icehawk/icehawk/compare/v1.3.1...v1.4.0
[1.3.1]: https://github.com/icehawk/icehawk/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/icehawk/icehawk/compare/v1.0.0...v1.3.0

[#31]: https://github.com/icehawk/icehawk/issues/31
[#28]: https://github.com/icehawk/icehawk/issues/28
[#27]: https://github.com/icehawk/icehawk/issues/27
[#26]: https://github.com/icehawk/icehawk/issues/26
[#24]: https://github.com/icehawk/icehawk/issues/24
[#23]: https://github.com/icehawk/icehawk/issues/23
[#22]: https://github.com/icehawk/icehawk/issues/21
[#20]: https://github.com/icehawk/icehawk/issues/20
[#18]: https://github.com/icehawk/icehawk/issues/18
[#17]: https://github.com/icehawk/icehawk/issues/17
[#16]: https://github.com/icehawk/icehawk/issues/16
[#15]: https://github.com/icehawk/icehawk/issues/15
[#13]: https://github.com/icehawk/icehawk/issues/13
[#9]: https://github.com/icehawk/icehawk/issues/9
[#8]: https://github.com/icehawk/icehawk/issues/8
[#2]: https://github.com/icehawk/icehawk/issues/2
[#1]: https://github.com/icehawk/icehawk/issues/1
