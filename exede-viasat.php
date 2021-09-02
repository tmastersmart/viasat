<?php
// --------------------------------------------------
// Exede Viasat DAP logon script   (C) 2021 v1
// --------------------------------------------------
//
// project URL
// https://github.com/tmastersmart/viasat
//
// copy updates from this URL
// https://raw.githubusercontent.com/tmastersmart/viasat/main/exede-viasat.php
//
// copyright 2021 by winnfreenet.com all rights recerved.
// works great with duckdns.org to set your ip and logon at the same time.
// but any url will work
// 
$ip = "www.duckdns.org";
$url="/update?domains=______&token=____________________";





$agent="mmexedescript v1";$phpVersion= phpversion();
print "===============================================================
";
print "Exede DAP script (c)2021 by winnfreenet.com all rights reserved
";
print "$agent PHP:$phpVersion
";
print "===============================================================
";
$datum = date('[H:i:s]');
print "$datum Posting $ip->";
$error = ""; $getheader = false; $htmlON=false;
$html = http_request('GET', $ip, 80 , $url);
// --------------------------
// bump my ISPS
// ViaSat login prompt
//
$exede = strpos($html, 'notice.exede.net');
if($exede){
  print" exede DAP ";
  $html = http_request('GET', "notice.exede.net", 80 , "/dap-redirect.php?host=$ip&url=$url");print"-"; 
  $html = http_request('GET', "notice.exede.net", 80 , "/dap/assets/style.css");print"-"; 
  $html = http_request('GET', "notice.exede.net", 80 , "/PolicyCheck.png");print"-"; 
// repost --- no error checking
  $html = http_request('GET', $ip, 80 , "$url");print"-"; 
 }

print "ok";




function http_request(
    $verb = 'GET',             /* HTTP Request Method (GET and POST supported) */
    $ip,                       /* Target IP/Hostname */
    $port = 80,                /* Target TCP port */
    $uri = '/',                /* Target URI */
    $getdata = array(),        /* HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $postdata = array(),       /* HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $cookie = array(),         /* HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $custom_headers = array(), /* Custom HTTP headers ie. array('Referer: http://localhost/ */
    $timeout = 5000,           /* Socket timeout in milliseconds */
    $req_hdr = false,          /* Include HTTP request headers */
    $res_hdr = false           /* Include HTTP response headers */
    )
{
global $BasicA,$agent,$version,$getheader,$htmlON,$responceHeader,$Postit,$req,$exede,$exedeCookie1,$exedeCookie2;

 $postdata_str="";


    if($Postit) {$postdata=$Postit;}

    if ($getheader){$res_hdr = true; }
    $ret = '';
    $verb = strtoupper($verb);
    $cookie_str = '';
    $getdata_str = count($getdata) ? '?' : '';
//    $postdata_str = '';
if (!$postdata_str){$postdata_str = '';}

    foreach ($getdata as $k => $v)
        $getdata_str .= urlencode($k) .'='. urlencode($v).'&';

    foreach ($postdata as $k => $v)
        $postdata_str .= urlencode($k) .'='. urlencode($v) .'&';

    foreach ($cookie as $k => $v)
        $cookie_str .= urlencode($k) .'='. urlencode($v) .'; ';

    $crlf = "\r\n";
//    $req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf;

    $req = $verb .' '. $uri .' HTTP/1.1' . $crlf;
    $req .= 'Host: '. $ip . $crlf;
    $req .= 'Connection: close' . $crlf;
    $req .= 'User-Agent: Mozilla/5.0 '. $agent . $crlf;
if ($BasicA){$req .= 'Authorization: Basic '. $BasicA . $crlf;}    $req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf;
    $req .= 'Accept-Language: en-us,en;q=0.5' . $crlf;
//    $req .= 'Accept-Encoding: deflate' . $crlf;
    $req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;

    foreach ($custom_headers as $k => $v)
        $req .= $k .': '. $v . $crlf;

    if (!empty($cookie_str))
        $req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf;

    if ($exede){
       $req .= "Cookie: _ga=$exedeCookie1" . $crlf;
       $req .= "Cookie: _mkto_trk=$exedeCookie2" . $crlf;
    }

    if ($verb == 'POST' && !empty($postdata_str))
    {
        $postdata_str = substr($postdata_str, 0, -1);
        $req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf;
        $req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf;
        $req .= $postdata_str;
    }
    else $req .= $crlf;

    if ($req_hdr)
        $ret .= $req;

    if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false){
    if ($errno=10060){$errstr="Timed Out";}
    return " error! $errno: $errstr";
    }
    fputs($fp, $req); //print "$req

    stream_set_timeout($fp, 0, $timeout * 1000);
        $ret = fgets($fp); $responceHeader =$ret; // gets responce header
// if is a webpage stop loading at the /html Prevents looping.
 while ($line = fgets($fp)) {
    $ret .= $line;
    if($htmlON) {$EndOfLine = strpos($line, '/html>'); if ($EndOfLine) { break;}}
  }
    fclose($fp);
    if (!$res_hdr){ $ret = substr($ret, strpos($ret, "\r\n\r\n") + 4);}
    return $ret;
}
