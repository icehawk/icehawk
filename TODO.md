# TODO

* Allow to configure a base URI of the web app
* Allow to configure a sub folder for request handlers - add an autoload interface and replace the domain namespace with it
* Add CSRF form handling
* Add generic accessors to session registry for form objects

```
- Form::new(id)
 |- Id
 |- Token
 |- DefaultData
 |- Data
 `- Feedback
```
