# CHANGELOG

## Changes in Version 1.3.0

 * Added `getRawData()` method to `PostRequest`, serving the raw POST data (`php://input`).

 * Removed the following methods from `DomainCommand` to avoid hard coded POST parameter names:
   * `hasSuccessUrl()`
   * `getSuccessUrl()`
   * `hasFailUrl()`
   * `getFailUrl()`
   
 * Added the follwing protected methods to `DomainCommand` to give access to all POST request data:
   * `getRequestData()` serves the whole POST request data array
   * `getRequestRawData()` serves the raw post data (`php://input`)
   * `getAllUploadedFiles()` serves all uploaded files as an assoc. array wrapped in `UploadedFileInfo` objects.
   * `getUploadedFiles($key)` serves all uploaded files for a certain key as num. array wrapped in `UploadedFileInfo` objects.
   * `getOneUploadedFile($key, $fileIndex = 0)` serves one uploaded file for a certain key and num. index wrapped in an `UploadedFileInfo` object. Or `NULL` if there is no file at `$key` and/or `$fileIndex`.
   
 * Added the following protected methods to `DomainQuery` to give access to all GET request data:
   * `getRequestData()` serves the whole GET request data array
   
 * Removed `exit()` from `Unauthorized` response.
 
 * Declared `SessionRegistry` as abstract.
 
 * Added the following methods to `RequestInfo` to access the basic auth data:
   * `getAuthUser()`
   * `getAuthPassword()`
   
 * Removed hard coded default setup for error handling and session in `IceHawkDelegate`. Now these settings are based on the system's php defaults.
 
 * Added more unit tests and clover code coverage.