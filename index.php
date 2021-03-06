<?php		
ini_set('display_errors', 1);
	
require_once 'libraries/httpProxyClass.php';
require_once 'libraries/cloudflareClass.php';

$httpProxy   = new httpProxy();
$httpProxyUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';

$requestLink = $_GET['u'];
$requestPage = json_decode($httpProxy->performRequest($requestLink));

if($requestPage->status->http_code == 200){
		echo $requestPage->content;
}
// if page is protected by cloudflare
else if($requestPage->status->http_code == 503 || $requestPage->status->http_code == 403) {
	
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