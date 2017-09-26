<?php		
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require_once 'libraries/httpProxyClass.php';
require_once 'libraries/cloudflareClass.php';

$httpProxy   = new httpProxy();
$httpProxyUA = 'proxyFactory';

$requestLink = $_GET['u'];
$requestPage = json_decode($httpProxy->performRequest($requestLink));

if($requestPage->status->http_code == 200){
		echo $requestPage->content;
}
// if page is protected by cloudflare
else if($requestPage->status->http_code == 503) {
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