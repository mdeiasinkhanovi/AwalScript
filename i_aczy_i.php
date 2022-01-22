<?php
error_reporting(0);
/*
if($thost!="freenetbd.cf"){

//download off
header('Content-type: image/png');
sleep(5);
exit;
//end download off

}//not freenet bd
*/

require 'config.php';
require 'head.php';
//max file size in config

$id=$_GET["id"];
$from=$_GET["from"]+0;
$buffer=1024*1000-1;
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


if($_GET["file_size"]>$max_file_size||$from>$max_file_size){
header('Content-type: image/png');
sleep(10);
exit;
}



$saved_size=filesize("downloads/".$id."/file.zip");

if($saved_size>0){
header('Content-type: image/jpg');

$result=file_get_contents("downloads/".$id."/file.zip", FALSE, NULL, $from,($buffer+1));

echo $result;
}
else if(strlen($link)>1){
header('Content-type: image/jpg');
$range=$from."-".($from+$buffer);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RANGE, $range);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
$httpcode=curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($ch);

$result=substr($result, 0, $buffer);

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
header('Content-type: image/png');
}
//els



exit;