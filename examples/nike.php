<?php

/**
 * Example of retrieving an authentication token of the Nike+ service
 *
 * PHP version 5.4
 *
 * @author     Andrii Gakhov <andrii.gakhov@gmail.com>
 * @copyright  Copyright (c) 2014 The author
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Nike;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['nike']['key'],
    $servicesCredentials['nike']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Nike+ service using the credentials, http client and storage mechanism for the token
/** @var $nikeService Nike */
$nikeService = $serviceFactory->createService('Nike', $credentials, $storage);

if (!empty($_GET['code'])) {
    // This was a callback request from Nike+, get the token
    $nikeService->requestAccessToken($_GET['code']);

    // Send a request with it
    $result = json_decode($nikeService->request('me/sport'), true);

    // Show some of the resultant data
    echo 'Your experience type is: ' . $result['summaries']['experienceType'];
} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $nikeService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Nike+!</a>";
}
