<h1>PHP Canvas</h1>
This is a very small example of using PHP with Force.com Canvas.  

<h1>PHP with the Canvas Javascript SDK</h1>
Canvas provides a JavaScript SDK if you want to make only minimal changes or installation in order to get up and running.  Effectively, this means you only need PHP to decode the incoming SignedRequest, and then hand the information to the client for JavaScript to use.  From there, the SDK can use window.postMessage (or the equivalent) to avoid any potential cross-domain issues, allowing you to make RESTful calls back to the Force.com platform via JavaScript.

index.php provides and example of that.

<h1>PHP with httpful</h1>
Or, if you want to keep the callbacks in PHP (perhaps for tighter integration with existing code), you can use a library like httpful to easily make REST callouts.  Normally httpful is distributed via a phar, but is included here as Heroku (which is optional to use with Canvas) does not support phar's normally.

index-httpful.php is an example of that.

<B>Note:</B> This is using/includes v26 of the Canvas SDK.  An upgrade to the latest, v27, will happen with Spring '13 is full released.