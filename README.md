[![Latest Stable Version](https://img.shields.io/packagist/v/webclient/ext-redirect.svg?style=flat-square)](https://packagist.org/packages/webclient/ext-redirect)
[![Total Downloads](https://img.shields.io/packagist/dt/webclient/ext-redirect.svg?style=flat-square)](https://packagist.org/packages/webclient/ext-redirect/stats)
[![License](https://img.shields.io/packagist/l/webclient/ext-redirect.svg?style=flat-square)](https://github.com/phpwebclient/ext-redirect/blob/master/LICENSE)
[![PHP](https://img.shields.io/packagist/php-v/webclient/ext-redirect.svg?style=flat-square)](https://php.net)

# webclient/ext-redirect

Redirect extension for PSR-18 HTTP client. 

# Install

Install this package and your favorite [psr-18 implementation](https://packagist.org/providers/psr/http-client-implementation).

```bash
composer require webclient/ext-redirect:^2.0
```

# Using

```php
<?php

use Webclient\Extension\Redirect\RedirectClientDecorator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/** 
 * @var ClientInterface $client Your PSR-18 HTTP Client
 * @var int $maxRedirects Max follow redirects
 */
$http = new RedirectClientDecorator($client, $maxRedirects);

/** @var RequestInterface $request */
$response = $http->sendRequest($request);
```
