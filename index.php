<?php		
<<<<<<< HEAD
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

=======
ini_set('display_errors', 1);
	
>>>>>>> 3b0330a5c8f89a223d7f518db6075b699d03e7a9
require_once 'libraries/httpProxyClass.php';
require_once 'libraries/cloudflareClass.php';

$httpProxy   = new httpProxy();
$httpProxyUA = 'proxyFactory';

$requestLink = $_GET['u'];
$requestPage = json_decode($httpProxy->performRequest($requestLink));

<<<<<<< HEAD
if($requestPage->status->http_code == 200){
		echo $requestPage->content;
}
// if page is protected by cloudflare
else if($requestPage->status->http_code == 503) {
=======
// if page is protected by cloudflare
if($requestPage->status->http_code == 503) {
>>>>>>> 3b0330a5c8f89a223d7f518db6075b699d03e7a9
	// Make this the same user agent you use for other cURL requests in your app
	cloudflare::useUserAgent($httpProxyUA);
	
	// attempt to get clearance cookie	
	if($clearanceCookie = cloudflare::bypass($requestLink)) {
		// use clearance cookie to bypass page
		$requestPage = $httpProxy->performRequest($requestLink, 'GET', null, array(
			'cookies' => $clearanceCookie
		));
		// return real page content for site
		$requestPage = json_decode($requestPage);
		echo $requestPage->content;
	} else {
		header("HTTP/1.1 503 Service Temporarily Unavailable");
		header('Status: 503 Service Temporarily Unavailable');
		echo 'Could not fetch CloudFlare clearance cookie (most likely due to excessive requests)';
	}	
}