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

# Authors and Contributors
Created in Jan 2016 by Alex Fiorello (@AlexFiorello)

# License

This package is licensed under LGPL. You are free to use it in personal and commercial projects. The code can be forked and modified, but the original copyright author should always be included!
