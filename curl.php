<?php
require 'config.php';
//max file size in config


    function formatBytes($size, $precision = 2)
    {
    	$base = log($size, 1024);
    	$suffixes = array('B', 'K', 'M', 'G', 'T');   
    
    	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

function removeHash($url){
$u=explode("#",$url,2);
return $u[0];
}
function getHash($url){
$u=explode("#",$url,2);
if(strlen(trim($u[1]))>0){ return "#".trim($u[1]);}
return "";
}


function load_url($url,$method,$fields,$cookie_file,$setting){
global $proxy_server;
global $use_proxy;
global $max_file_size;

$returning_str="";
$start_time=time();



$curl = curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl,CURLOPT_URL, $url);

//cookie
curl_setopt($curl,CURLOPT_VERBOSE, true);
if($setting["cookie"]=="yes"){
curl_setopt($curl,CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie_file); }


//method
if($method=="post"){
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt( $curl, CURLOPT_POSTFIELDS, $fields);
}


//proxy
if($use_proxy==true){
curl_setopt($curl, CURLOPT_PROXY, $proxy_server);
curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
}



//user agent
$agent = $setting["useragent"];
curl_setopt($curl, CURLOPT_USERAGENT, $agent);


curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($curl, CURLOPT_MAXFILESIZE,1024*850);

function readHeader($ch, $header)
{
     // read headers
  //  echo "Read header: ", $header."<br>";
          global $responseHeaders;
          global $url_type;
          global $setting;
          global $limit_over;
          global $url_location;
          global $url_error;
          global $max_file_size;
          global $store_file;
          global $thost;//this host
          

          $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
          $responseHeaders[$url][] = $header;
//echo strlen($header)."<br>".$url_type."<br>";

if($url_type!="html"&&strlen($header)==2){
foreach($responseHeaders[$url] as $rr){
if(stripos(trim($rr),"content-length")===0){
$ccg=explode(":",$rr);
$cc=trim($ccg[1]); }
if(stripos(trim($rr),"Content-Disposition")===0){
$ccg=explode("filename=",$rr);
$cy=explode(";",$ccg[1]);
$cn=$cy[0];
$cn=trim($cn);
$cn=str_replace('"','',$cn);
$cn=str_replace("'","",$cn);
$cn=$cn; }


if(stripos(trim($rr),"location")===0){
$ccg=explode(":",$rr,2);
$cj=trim($ccg[1]);
if($cj!=$url){header("location:index.php?get_post_url=".urlencode($cj));exit;}
}

}//foreach
$base=explode("?",basename($url));
$file["name"]=$base[0];

if(!empty($cn))$file["name"]=$cn;

if($setting["download"]=="yes"){
echo "<title>Download ".$file['name']."..</title>";
echo "Download-able File: ".urldecode($file['name'])."<br>";

/*
if($thost!="freenetbd.cf"){

//downlod off
echo "সাময়িক সমস্যার জন্য ডাউনলোড বন্ধ";
exit;
//end downoad off temp

}//not free net bd
*/

if($cc>$max_file_size){
echo "Limit is: ".formatBytes($max_file_size,2)."<br>"; }
else{
if($store_file==true){
echo '<a class="button-submit" href="download.php?file_link='.urlencode($url).'&file_name='.urlencode($file['name']).'&file_size='.urlencode($cc).'&direct=true&maxtime=true">Save to Server(Max)</a><br>';
echo '<a class="button-submit" href="download.php?file_link='.urlencode($url).'&file_name='.urlencode($file['name']).'&file_size='.urlencode($cc).'&direct=true&maxtime=false">Save to Server(Normal)</a><br>';
}//if true
if($cc>0){ echo '<a href="i_app_i.php?id='.time().'_'.rand(1000,999999999).'&file_name='.$file["name"].'&file_size='.$cc.'&from=__D__&file_link='.urlencode($url).'&fine=ok"><font color=red>Long click To Download</font></a><br>'; }
else{
echo "<font color=red>it is not possible to download directly while file size is unknown</font><br>";
}

}//else


echo "File Size: ".formatBytes($cc,2)."<br>"; 
exit;
}
if($cc>1024*850){
$limit_over=true; return 0;}


}


   if(stripos($header,"content-type")===0){
if(stripos(trim($header),"html")>0){ $url_type="html"; }
            else{
      $url_type="file";}
            }
      return strlen($header);
}
//get body and headers
curl_setopt($curl,CURLOPT_HEADER, true);
curl_setopt($curl,CURLOPT_NOBODY, false);
curl_setopt($curl,CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_HEADERFUNCTION, "readHeader");



$ch = curl_exec($curl);


$header_len = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($ch, 0, $header_len);
$body = substr($ch, $header_len);

$page["time"]=time()-$start_time+0.1;
$page["size"]=formatBytes(strlen($body)+1,2);


if(empty($ch)){
$returning_str.= "Connection Error....<br>";
}

$redir=$url;
$redir = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);


$httpcode=curl_getinfo($curl, CURLINFO_HTTP_CODE);


$contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

if(stripos("@".$url_type,'html')>0){}
elseif(strlen($url_type)==0){ } else{ $returning_str.= "its not a html...<br>"; }


if($ch==false&&stripos(curl_error($curl),"writing")==false){
$returning_str.="Error:".curl_error($curl).".<br>";
}

if(stripos(curl_error($curl),"writing")!=false){
$returning_str.= "Browser loading capacity is only 1MB...<br>";
}

if(empty($body)){
$returning_str.="Empty Body...<br>";
}


curl_close($curl); 



if($redir!=$url){
header("location:index.php?get_post_url=".urlencode(removeHash($redir)).getHash($redir));
exit;
}


/*
if($setting["source"]=="yes") {
$body=htmlentities($body,ENT_QUOTES);
}*/



END:

$data=(strlen(trim($returning_str))>0)?$returning_str : $body;




$browse_form='<form method="get" action="index.php" class="fffg_body">
<div class="fffg_ac">
<div class="fffg_menu">
<input type="text" name="get_post_url" value="'.$url.'" style="width:auto;margin-bottom:5px;"><br>
<input type="submit" name="get_post_submit" value="Go > > >" class="fffg_submit"/>...[<a href=index.php>Home</a>]<span style="float:right"><small>'.$page["size"].' - '.$page["time"].' sec</small> - <a href="set.php">Setting</a></span></div></div></form>';

//is browse from is requested or not
if($setting["hide_browse_form"]=="yes")$browse_form="";

if(strlen(trim($returning_str))>0){
echo "<html><head>";
include 'header.php';
echo "</head><body>".$browse_form;
echo $data;
}else{
//prepare the data
include 'prepare.php';
$data=prepare($data,$url,$browse_form,$setting);
echo $data;
}


}





function testint($int,$r){


if (!filter_var($int, FILTER_VALIDATE_INT) === false) {

if($int>=1){      return $int;}
else{return "1";}

} else {  return $r;}


}