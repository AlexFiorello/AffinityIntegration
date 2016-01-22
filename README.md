## Affinity Integration
Affinity Integration gives you access to the Affinity API to perform general tasks. It includes the ability to create tickets, view records, update details.
```php
Affinity::login();
$siteID = Affinity::getSiteId();
Affinity:::logout();
```
---
[![Latest Stable Version](https://poser.pugx.org/fiorello/affinity-integration/v/stable)](https://packagist.org/packages/fiorello/affinity-integration) [![Total Downloads](https://poser.pugx.org/fiorello/affinity-integration/downloads)](https://packagist.org/packages/fiorello/affinity-integration) [![Latest Unstable Version](https://poser.pugx.org/fiorello/affinity-integration/v/unstable)](https://packagist.org/packages/fiorello/affinity-integration) [![License](https://poser.pugx.org/fiorello/affinity-integration/license)](https://packagist.org/packages/fiorello/affinity-integration)

# Installation
Require this package in your `composer.json` and update composer. This will download the package.
```php
"fiorello/affinity-integration": "dev-master"
```
 After updating composer, add the ServiceProvider to the providoers array in `app/config/app.php`
```php
'fiorello\AffinityIntegration\AffinityIntegrationServiceProvider',
```

You will need to publish the configuration, and amend it.
```php
"php artisan config:publish fiorello/affinity-integration"
```

Then go to:  `app/config/packages/fiorello/affinity-integration/config.php`  and enter your own Affinity API connection details.

```php
return array(
    'affinityWSDL'      =>  "https://api.affinity.akjl.co.uk/[APIuser]/WSDL/AffinityAPIService.WSDL",
    'affinityUserName'  =>  "[AffinityUsername]",
    'affinityPassword'  =>  "[AffinityPassword]",
    'affinityAppName'   =>  "[AppName]",
    'affinityAppRef'    =>  "[LaravelAPI]"
);
```
---
# Usage
Before you can make any calls to Affinity, you must login.  This process gets a unique Token which will be used for all subsequent calls automatically.  You must remember to log out when you have finished.

```php
Affinity::login();
//Do Stuff
Affinity::logout();
```

There are two types of result returned from a function call.  Where a function call was succesful the following properties will be returned (`errorCode` will be equal to `0`):
```php
errorCode
result
properties
```
Where a function call fails the following is returned:
```php
errorCode
errorMessage
```

The `result` property will either be a string, integer or an object.  Where `result` is an object the `properties` property will be returned as an array with the available properties for the `result` object. You can use this if you do not know what data the object contains.

#An Example
```php
Affinity::login()
$siteResponse = Affinity::getSiteId("HT123456");
if($siteResponse->errorCode > 0) {
 //An error has occured, display the message
 echo $siteResponse->errorMessage;
} else {
 //All processed fine, lets handle the data.  
 //I know that getSiteId returns an Integer.
 //So use this to get Site Details
 $detailsResponse = Affinity::getSite($siteResponse->result);
 if($detailsResponse->errorCode > 0) {
  //An error occured so display the message
  echo $detailsResponse->errorMessage;
 } else {
  //getSite returns an object of site details
  var_dump($detailsResponse->result);
 }
 
 
```



# Authors and Contributors
Created in Jan 2016 by Alex Fiorello (@AlexFiorello)

# License

This package is licensed under LGPL. You are free to use it in personal and commercial projects. The code can be forked and modified, but the original copyright author should always be included!
