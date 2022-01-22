<?php
error_reporting(0);
require 'config.php';
//max file size in config

$id=$_GET["id"];
$from=$_GET["from"]+0;
$buffer=1024*1000;
$size=$_GET["file_size"];
$link=$_GET["file_link"];
$CID="file_id_".$id;

/*
if($_COOKIE[$CID]>10){
//for file not found 404
header('Content-type: image/png');
exit;
}*/


function testint($int,$r){

if (!filter_var($int, FILTER_VALIDATE_INT) === false) {

if($int>=1){      return $int;}
else{return "1";}

} else {  return $r;}

}

$from=testint($from,0);


if(strlen($link)>1){
$range=$from."-";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RANGE, $range);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
$httpcode=curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($ch);

echo $result;

/*
if(empty($result)){ 
setcookie($CID, $_COOKIE[$CID]+1, time() + (600), "/");
sleep(7);
}else{
setcookie($CID, "0", time() + (600), "/");
}*/

}
else{
//for file not found 404
}
//els
exit;